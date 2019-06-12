import Vue from 'vue';
import EventBus from './event-bus';
import './bootstrap';
import './admin-lte.js';
import 'toastr';
import swal from 'sweetalert2';

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.component('vg-vuetable', require('./components/Vuetable'));
Vue.component('user-table', require('./components/UserTable'));
Vue.component('filter-bar', require('./components/FilterBar'))

new Vue({
    el: '#app'
});

$(window).on('load', function(){
    $('#loader').fadeOut();
});

//Filter Bar
let selectFilter = document.getElementById('js-select-filter');
if (selectFilter) {
    let inputFilter = document.getElementById('js-input-filter');
    selectFilter.addEventListener('change', () => {
        inputFilter.name = selectFilter.value;
    });
}

// Sweet alert configuration
let button = $(".js-button-delete");
button.on('click',function(event){
    event.preventDefault();
    let form = $(this).parent(".form-delete");

    swal({
        title: "¿Estás seguro?",
        text: "¡No podrás revertir esto!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
    }).then( result => {
        if (result.value) {
            form.submit();
            return;
        }
       swal('¡Cancelado!', 'Acción cancelada.', 'error');
    });
}); //FIN ON CLICK

