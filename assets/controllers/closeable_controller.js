import {Controller} from '@hotwired/stimulus';
import {useTransition} from 'stimulus-use';

export default class extends Controller {
    static values = {
        autoClose: Number,
    };

    static targets = ['timerbar']
    animationRequest = null
    animationStoppedAt = null
    connect() {
        useTransition(this, {
            element: this.element,
            leaveActive: 'transition ease-in duration-1500',
            leaveFrom: 'opacity-100',
            leaveTo: 'opacity-0',
            transitioned: true,
        })


        if (this.autoCloseValue) {
            this.startTimeout(this.autoCloseValue)
            this.timebarInitialTime = window.performance.now()
            this.renderTimerbar()

        }

        this.element.addEventListener("mouseover", () => {
            clearTimeout(this.timeout)
            this.timeout = null
            // "Freeze" the timeout
            if (this.animationRequest) {
                window.cancelAnimationFrame(this.animationRequest)
                this.animationStoppedAt = window.performance.now()
                this.animationRequest = null
            }
        })
        this.element.addEventListener("mouseleave", () => {
            if (!this.timeout) {
                this.startTimeout(Math.max(0,this.autoCloseValue - this.elapsed))
            }
            if (!this.animationRequest) {
                this.timebarInitialTime += window.performance.now() - this.animationStoppedAt
                this.renderTimerbar()
            }
        })

    }


    startTimeout(timeout) {
        // Resume timeout where it was
        this.timeout = setTimeout(() => {
            this.close()
        }, timeout)
    }

    renderTimerbar(timestamp = 0) {
        if (isNaN(this.autoCloseValue) || !this.hasTimerbarTarget || this.timebarInitialTime === undefined) {
            return;
        }

        this.elapsed = (timestamp - this.timebarInitialTime)

        const remaining = parseInt(this.autoCloseValue) - this.elapsed;

        const width = remaining / parseInt(this.autoCloseValue);

        if (isNaN(width) || width < 0) {
            return;
        }

        this.timerbarTarget.style.width = (width * 100) + '%'
        if (width > 0) {
            this.animationRequest = window.requestAnimationFrame(this.renderTimerbar.bind(this))
        }

    }

    close() {
        this.timebarInitialTime = undefined
        this.leave()

    }
}