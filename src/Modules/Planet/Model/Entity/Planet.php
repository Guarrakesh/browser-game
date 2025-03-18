<?php

namespace App\Modules\Planet\Model\Entity;

use App\Modules\Planet\Dto\ObjectDefinition\Building\BuildingDefinition;
use App\Modules\Planet\Dto\PlanetDTO;
use App\Modules\Planet\Infra\Repository\PlanetRepository;
use App\Modules\Planet\Model\Exception\InvalidBuildingConfigurationException;
use App\Modules\Planet\Model\Location;
use App\Modules\Planet\Model\Storage;
use App\Modules\Shared\Constants;
use App\Modules\Shared\Dto\GameObject;
use App\Modules\Shared\Dto\GameObjectLevel;
use App\Modules\Shared\Exception\EnqueueException;
use App\Modules\Shared\Exception\InsufficientResourcesException;
use App\Modules\Shared\Exception\RequirementsNotMetException;
use App\Modules\Shared\Model\ObjectType;
use App\Modules\Shared\Model\ResourcePack;
use AutoMapper\Attribute\Mapper;
use AutoMapper\Attribute\MapTo;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Timestampable;
use Symfony\Component\Clock\Clock;

#[Mapper(target: [PlanetDTO::class, 'array'])]
#[ORM\Entity(repositoryClass: PlanetRepository::class)]
class Planet
{
    private const RESOURCE_BUILDINGS = [Constants::CONCRETE_EXTRACTOR, Constants::METAL_REFINERY, Constants::CIRCUIT_ASSEMBLY_PLANT, Constants::HYDROPONIC_FARM];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Embedded(class: Location::class)]
    private Location $location;

    #[ORM\Column]
    private ?int $points = 0;

    #[ORM\Column]
    private ?bool $isActive = true;

    /** @var Collection<PlanetConstruction> */
    #[ORM\OneToMany(targetEntity: PlanetConstruction::class, mappedBy: 'planet', cascade: ['persist', 'remove'])]
    #[ORM\OrderBy(['completedAt' => 'ASC'])]
    private Collection $constructions;

    #[ORM\Embedded(class: Storage::class, columnPrefix: "storage_")]
    #[MapTo(transformer: 'source.getStorageAsPack()')]
    private ?Storage $storage;

    /**
     * @var Collection<int, PlanetBuilding>
     */
    #[ORM\OneToMany(targetEntity: PlanetBuilding::class, mappedBy: 'planet', cascade: ['persist', 'remove'], orphanRemoval: true, indexBy: 'name')]
    #[MapTo(target: PlanetDTO::class, property: 'buildings')]
    /** @var Collection<string, PlanetBuilding> $planetBuildings */
    private Collection $planetBuildings;

    #[ORM\Column]
    private int $playerId;

    #[ORM\Column(nullable: true)]
    #[Timestampable]
    private ?DateTimeImmutable $updatedAt = null;
    #[ORM\Column(length: 255, options: ['default' => 0])]
    private int $dronesCount = 0;

    private ?ConstructionQueue $constructionQueue = null;

    public function __construct(int $playerId, string $name, Location $location, ?int $initialStorage = 30)
    {
        $this->playerId = $playerId;
        $this->name = $name;
        $this->storage = new Storage($initialStorage);
        $this->planetBuildings = new ArrayCollection();
        $this->constructions = new ArrayCollection();
        $this->constructionQueue = new ConstructionQueue();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }


    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function getStorageAsPack(): ResourcePack
    {
        return $this->storage->getAsPack();
    }

    public function getDronesCount(): int
    {
        return $this->dronesCount;
    }



    public function getMaxStorage(): int
    {
        $storageBay = $this->getBuilding(Constants::STORAGE_BAY);
        $storageBayDefinition = $storageBay->getDefinition();
        $increaseFactor = $storageBayDefinition->findParameter('storage_increase_factor');
        $baseStorage = $storageBayDefinition->findParameter('base_storage');
        $maxLevel = $storageBayDefinition->getMaxLevel();
        if (!$increaseFactor || !$baseStorage || !$maxLevel) {
            throw new \LogicException("Cannot upgrade storage of Storage for configuration error.");
        }

        $level = min($storageBay->getLevel(), $maxLevel);
        // TODO: Take into account current effects (like increase storage 10%)

        return $baseStorage * ($increaseFactor ** ($level - 1));
    }


    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }


    /** ===== CONSTRUCTIONS =====  */

    /**
     * - The current highest level is current building's level plus the number of enqueued upgrades.
     * - **Invariant 1:** The difference between the order level and the current highest level must be 1. (Cannot build 2+ levels at once.)
     * - **Invariant 2:** The upgrade order level cannot exceed the building's max level.
     */
    public function enqueueConstruction(BuildingDefinition $buildingDefinition, int $duration, int $level, ResourcePack $cost): void
    {
        if (!$this->areBuildingRequirementsMet($buildingDefinition)) {
            throw new RequirementsNotMetException(
                $buildingDefinition->getRequirements(),
                new GameObject($buildingDefinition->getName(), ObjectType::Building)
            );
        }

        $nextLevel = $this->getNextLevelForBuilding($buildingDefinition);
        if ($level !== $nextLevel) {
            // Invariant 1
            throw new EnqueueException("Invalid level in ordered construction.");
        }

        if ($nextLevel > $buildingDefinition->getMaxLevel()) {
            // Invariant 2
            throw new EnqueueException("Max level reached.");
        }


        $construction = new PlanetConstruction($this, $buildingDefinition, $level, $cost, false);

        $this->getConstructionQueue()->enqueue($buildingDefinition, $construction, $duration);
        $this->constructions->add($construction);
    }


    /**
     *  - The current lowest level the current building level minus the number of enqueued downgrades.
     *  - **Invariant 1:** The difference between the order level and the current highest level must be 1. (Cannot demolish 2+ levels at once.)
     *  - **Invariant 2:** The downgrade order level cannot exceed the building's min level.
     */
    public function enqueueDemolition(BuildingDefinition $buildingDefinition, int $duration, int $level, ResourcePack $cost): void
    {
        if (!$this->areBuildingRequirementsMet($buildingDefinition)) {
            throw new RequirementsNotMetException(
                $buildingDefinition->getRequirements(),
                new GameObject($buildingDefinition->getName(), ObjectType::Building)
            );
        }


        $prevLevel = $this->getPreviousLevelForBuilding($buildingDefinition);
        if ($level !== $prevLevel) {
            // Invariant 1
            throw new EnqueueException("Invalid level in ordered construction.");
        }

        if ($prevLevel < 0) {
            // Invariant 2
            throw new EnqueueException("Min level reached.");
        }


        $construction = new PlanetConstruction($this, $buildingDefinition, $level, $cost, true);

        $this->getConstructionQueue()->enqueue($buildingDefinition, $construction, $duration, true);
        $this->constructions->add($construction);

    }

    public function cancelConstruction(int $constructionId): void
    {
        $construction = $this->getConstructionQueue()->getConstructionById($constructionId);
        $cancelled = $this->getConstructionQueue()->cancel($construction);

        // Give back 90% of resources.
        $this->creditResources($cancelled->getResourcesUsed()->multiply(0.9));
    }
    public function terminateConstruction(int $constructionId): void
    {
        $construction = $this->getConstructionQueue()->getConstructionById($constructionId);
        $this->getConstructionQueue()->terminate($construction);
        $this->upgradeBuilding($construction->getDefinition(), $construction->getLevel());
    }

    /**
     * Check if requirements to build this building are met.
     * TODO: consider if it's better to move this into a DomainService
     * @param BuildingDefinition $definition
     * @return bool
     */
    public function areBuildingRequirementsMet(BuildingDefinition $definition): bool
    {
        $requirements = $definition->getRequirements();
        foreach ($requirements as $requirement) {
            $building = $this->getBuilding($requirement->getObject()->getName());
            // TODO: handle research requirements

            if (!$building || ($requirement->getLevel() > $building->getLevel())) {
                return false;
            }

        }

        return true;
    }

    /**
     * @param BuildingDefinition $buildingDefinition
     * @param int $level The level to which upgrade the building.
     * If the level given is less than current level, the building is downgraded.
     * If the downgraded level is less than the building min level, the building is considered removed.
     * @return void
     */
    public function upgradeBuilding(BuildingDefinition $buildingDefinition, int $level): void
    {
        $building = $this->getBuilding($buildingDefinition->getName());
        if (!$building) {
            $building = new PlanetBuilding($this, $buildingDefinition, $level);
        }
        $maxLevel = $buildingDefinition->getMaxLevel();
        if (!$maxLevel) {
            throw new InvalidBuildingConfigurationException($buildingDefinition, "max_level and/or min_level not found.");
        }

        if ($level > $buildingDefinition->getMaxLevel()) {
            // building fully built.
            return;
        }

        $level = max(0, min($level, $maxLevel));

        $building->setLevel($level);

        if ($level <= 0) {
            $this->planetBuildings->remove($building->getName());
            $building->setPlanet(null);
        } else {
            $this->planetBuildings->set($building->getName(), $building);
        }

        //TODO: Dispatch Building Upgrade or Completed event.
    }

    public function getBuildingLevel(string $buildingName): int
    {
        return $this->getBuilding($buildingName)?->getLevel() ?? 0;
    }

    public function hasBuilding(string $buildingName): bool
    {
        return $this->planetBuildings->containsKey($buildingName);
    }


    public function creditResources(ResourcePack $pack): void
    {
        $this->storage->addResources($pack, $this->getMaxStorage());
    }

    public function debitResources(ResourcePack $pack): void
    {
        if (!$this->storage->containResources($pack)) {
            throw new InsufficientResourcesException($pack);
        }

        $this->storage->subtractResources($pack, $this->getMaxStorage());

    }

    public function hasResources(ResourcePack $pack): bool
    {
        return $this->storage->containResources($pack);
    }
    public function hasStorageForPack(ResourcePack $pack): bool
    {
        return $this->storage->containResources($pack);
    }

    /**
     * @return ConstructionQueue The queue of the currently active (and unprocessed) construction jobs.
     */
    #[ORM\PostLoad]
    private function getConstructionQueue(): ConstructionQueue
    {
        if ($this->constructionQueue === null) {
            $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->isNull('cancelledAt'))
                ->andWhere(Criteria::expr()->eq('processed', false));
            $this->constructionQueue = new ConstructionQueue($this->constructions->matching($criteria)->toArray());
        }

        return $this->constructionQueue;
    }

    /** @return Collection<PlanetConstruction> */
    public function getQueuedJobs(): Collection
    {
        return new ArrayCollection($this->constructionQueue->getJobs());
    }


    public function processConstructions(int $timestamp): void
    {
        foreach ($this->getConstructionQueue()->processCompletedJobs($timestamp) as $job) {
            $this->upgradeBuilding($job->getDefinition(), $job->getLevel());
        }
    }


    /**
     * Returns the next possible level for a building, or its final level if the maximum has been reached.
     */
    public function getNextLevelForBuilding(BuildingDefinition $buildingDefinition): int
    {
        $buildingName = $buildingDefinition->getName();
        $currentLevel = $this->getBuildingLevel($buildingName);
        $numUpgradesInQueue = $this->getConstructionQueue()->getUpgradeCountsForBuilding($buildingName);

        return $currentLevel + $numUpgradesInQueue + 1;
    }

    public function getPreviousLevelForBuilding(BuildingDefinition $buildingDefinition): int
    {
        $buildingName = $buildingDefinition->getName();
        $currentLevel = $this->getBuildingLevel($buildingName);
        $numDowngradesInQueue = $this->getConstructionQueue()->getDowngradeCountsForBuilding($buildingName);

        return $currentLevel - $numDowngradesInQueue - 1;


    }

    public function processProduction(ResourcePack $production, ?DateTimeImmutable $at = null): void
    {
        $at = $at ?? Clock::get()->now();
        $lastUpdate = $this->storage->getUpdatedAt();

        $elapsedSeconds = $lastUpdate !== null
            ? $at->getTimestamp() - $lastUpdate->getTimestamp()
            : 0;

        $production = $production->toSeconds()
            ->multiply($elapsedSeconds);
        $this->storage->addResources($production, $this->getMaxStorage(), $at);

    }


    public function getBaseHourlyProduction(): ResourcePack
    {
        $pack = new ResourcePack();
        foreach (self::RESOURCE_BUILDINGS as $buildingName) {
            $building = $this->getBuilding($buildingName) ?? null;
            if (!$building) {
                continue;
            }

            $prodIncreaseFactor = $building->getDefinition()->findParameter('production_increase_factor');
            if (!$prodIncreaseFactor) {
                throw new InvalidBuildingConfigurationException($building->getDefinition(), "Missing production_increase_factor");
            }

            $pack = $pack->addFromBuilding(
                $buildingName,
                $building->getDefinition()->getHourlyProduction() * ($prodIncreaseFactor ** ($building->getLevel() - 1))
            );

        }

        return $pack;
    }

    private function getBuilding(string $name): ?PlanetBuilding
    {
        return $this->planetBuildings[$name] ?? null;
    }

    public function getBuildingAsGameObject(string $name): ?GameObjectLevel
    {
        return $this->getBuilding($name)?->getAsGameObjectLevel();
    }
    /**
     * @return Collection<GameObjectLevel>
     */
    public function getBuildingsAsGameObjects(): Collection
    {
        return $this->planetBuildings->map(
            fn(PlanetBuilding $pb) => $pb->getAsGameObjectLevel()
        );
    }
    public function getPlayerId(): int
    {
        return $this->playerId;
    }



}
