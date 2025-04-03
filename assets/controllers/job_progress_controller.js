import {Controller} from "@hotwired/stimulus"


export default class extends Controller {
    static values = {
        duration: Number,
        completedAt: Number,
        startedAt: Number
    }


    static targets = ["progressBar", "remaining"]

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
        if (this.startedAtValue > now) {
            return
        }
        const remaining = this.startedAtValue + this.durationValue - now
        const elapsed = 100 * ((now - this.startedAtValue) / this.durationValue)
        this.progressBarTarget.setAttribute('value', elapsed)
        this.progressBarTarget.style.width = elapsed + '%';

        if (this.remainingTarget) {
            this.remainingTarget.innerText = new Date(remaining * 1000).toISOString().slice(11, 19);
        }
        if (remaining <= 0) {
            this.isRunning = false
            return;
        }
        window.requestAnimationFrame(this.updateProgressValue.bind(this))
    }

    disconnect() {
        this.isRunning = false
    }
}