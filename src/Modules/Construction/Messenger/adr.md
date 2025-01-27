The messenger layer is the layer between the controller
and the application layer (services).
Its job is to relay the controller request to the services
by mapping the incoming DTO to actual Entities and mapping back Entities to DTOs
in the returned value.

The messenger has access to the service layer and the EntityServiceRepository classes
to find the Entities.

It uses the AutoMapper or manual mapping logic to map to and from DTOs.

This approach allows the Services to only deal with Business Domain Entities 
in both arguments and returned value, so they can communicate each other easier without
having to map to and from DTOs for internal calls.

### Messenger Method naming convention
The messenger should expose the methods in the convention `send{Command}Request(...args)` 
and usually returns a DTO object.

The arguments can be of any type of DTO, there is no convention of passing 
object such as "RequestDTO".
Same applies to response types.  