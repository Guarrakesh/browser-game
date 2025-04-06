import {Controller} from "@hotwired/stimulus"


export default class extends Controller {
    static values = {
        duration: Number,
        completedAt: Number,
        startedAt: Number
    }


    static targets = ["progressBar", "remaining"]
    isRunning = false

    connect() {
        if (!this.progressBarTarget) {
            return;
        }

        this.isRunning = true
        window.requestAnimationFrame(this.updateProgressValue.bind(this))


    }

    updateProgressValue() {
        if (!this.isRunning) {
            return
        }
        const now = Date.now() / 1000
        const completedAt = this.startedAtValue + this.durationValue
        if (this.startedAtValue > now) {
            // Job not started yet, come back later
            setTimeout(this.updateProgressValue.bind(this), this.startedAtValue - now)
            return
        }

        if (completedAt < now) {
            this.element.remove();
            this.isRunning = false
            return
        }


        const remaining = completedAt - now
        const elapsed = this.durationValue - remaining
        const elapsedPercentage = elapsed / this.durationValue
        this.progressBarTarget.setAttribute('value', elapsed)
        this.progressBarTarget.style.width = (100 * elapsedPercentage) + '%';

        if (this.hasRemainingTarget) {
            this.remainingTarget.innerText = new Date(remaining * 1000).toISOString().slice(11, 19);
        }
        if (remaining <= 0) {
            this.element.remove();
            return;
        }
        window.requestAnimationFrame(this.updateProgressValue.bind(this))
    }

    disconnect() {
        this.isRunning = false
    }
}