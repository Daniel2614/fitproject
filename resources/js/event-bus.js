import Vue from 'vue'

// Create a global Event Bus
let EventBus = new Vue();

// Add to Vue properties by exposing a getter for $bus
Object.defineProperties(Vue.prototype, {
    $eventHub: {
        get: function () {
            return EventBus
        }
    }
});