// app/javascript/controllers/tabs_controller.js
import {Controller} from "@hotwired/stimulus"

/**
 * thanks https://railsnotes.xyz/blog/simple-stimulus-tabs-controller
 */
// Connects to data-controller="tabs"
//
export default class extends Controller {
    static classes = ['active', 'currentTab']
    static targets = ["btn", "tab"]
    static values = {defaultTab: String}


    connect() {
        this.ids = []
        // first, hide all tabs
        this.tabTargets.forEach(x => {
            x.hidden = true
            this.ids.push(x.id)
        })

        // then, show the default tab
        let selectedTab = this.tabTargets.find(element => element.id === this.defaultTabValue)
        selectedTab.hidden = false
        selectedTab.classList.add(...this.currentTabClasses)
        // and activate the selected button
        let selectedBtn = this.btnTargets.find(element => element.dataset.targetId === this.defaultTabValue)
        selectedBtn.classList.add(...this.activeClasses)


    }

    // switch between tabs
    // add to your buttons: data-action="click->tabs#select"
    select(event) {
        // find tab matching (with same id as) the clicked btn
        let selectedTab = this.tabTargets.find(element => element.id === event.currentTarget.dataset.targetId)
        if (selectedTab && selectedTab.hidden) {
            // hide everything
            this.tabTargets.forEach(x => {
                x.hidden = true
                x.classList.remove(this.currentTabClasses)
            }) // hide all tabs
            this.btnTargets.map(x => x.classList.remove(...this.activeClasses)) // deactive all btns

            // then show selected
            selectedTab.hidden = false // show current tab
            selectedTab.classList.add(...this.currentTabClasses)

            event.currentTarget.classList.add(...this.activeClasses) // activate current button
        }
    }
}
