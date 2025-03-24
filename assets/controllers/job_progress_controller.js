import {Controller} from "@hotwired/stimulus"


export default class extends Controller {
    static values = {
        duration: Number,
        completedAt: Number
    }

    static targets = ["progressBar"]

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
        const remaining = this.completedAtValue - now
        const elapsed = Math.round((1 - (remaining / this.durationValue)) * 100)
        this.progressBarTarget.setAttribute('value', elapsed)
        this.progressBarTarget.innerText = elapsed + '%'
        this.progressBarTarget.style.width = elapsed + '%';

        window.requestAnimationFrame(this.updateProgressValue.bind(this))
    }

    disconnect() {
        this.isRunning = false
    }
}