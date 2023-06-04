/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

window.Vue = require('vue');
window._ = require('lodash');
window.axios = require('axios');


window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

import Echo from 'laravel-echo'

import Vue from 'vue';

import VueTextareaAutosize from 'vue-textarea-autosize'
import Editor from '@tinymce/tinymce-vue';
import Verte from 'verte';
import 'verte/dist/verte.css';
import VueSwal from 'vue-swal';

import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import 'vue-search-select/dist/VueSearchSelect.css';
import DisableAutocomplete from 'vue-disable-autocomplete';

import VueRecord from '@codekraft-studio/vue-record';
import AudioRecorder from 'vue-audio-recorder';

Vue.use(AudioRecorder);

Vue.use(DisableAutocomplete);


// register component globally
Vue.component(Verte.name, Verte);

Vue.use(VueTextareaAutosize);
Vue.use(Editor);
Vue.use(VueSwal);

Vue.use(VueRecord);

Vue.use(Vuetify);

const opts = {};

export default new Vuetify(opts);

window.Pusher = require('pusher-js');
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '6be490b9f6584a494233',
    cluster: 'ap2',
    encrypted: false
});

Vue.component('blackboard-process-editor', require('./components/ProcessEditor.vue').default);
Vue.component('blackboard-forms-editor', require('./components/FormEditor.vue').default);
Vue.component('blackboard-search', require('./components/Search.vue').default);
Vue.component('blackboard-notifications', require('./components/Notifications.vue').default);
Vue.component('blackboard-messages', require('./components/Messages.vue').default);
Vue.component('blackboard-wizard', require('./components/fa-details/FADetails.vue').default);
Vue.component('attooh-client-basket', require('./components/attooh-client-basket/Attooh-client-basket.vue').default);
Vue.component('attooh-trial-notification', require('./components/trial/Trial.vue').default);
Vue.component('blackboard-fa-details', require('./components/fa-details/FADetailsEdit.vue').default);
Vue.component('blackboard-fa-details-show', require('./components/fa-details/FADetailsShow.vue').default);
// Passport Client
Vue.component('passport-clients', require('./components/passport/Clients.vue').default);
Vue.component('passport-authorized-clients', require('./components/passport/AuthorizedClients.vue').default);
Vue.component('passport-personal-access-tokens', require('./components/passport/PersonalAccessTokens.vue').default);

Vue.component('board-component', require('./components/work-management-tool/Board.vue').default);
// You have to install sortable.js to use it:
// 'npm install sortablejs'


const createSortable = (el, options, vnode) => {
    return Sortable.create(el, {
        onEnd: function (evt) {
            const data = vnode.componentInstance.$data.activities
            const item = data[evt.oldIndex]
            if (evt.newIndex > evt.oldIndex) {
                for (let i = evt.oldIndex; i < evt.newIndex; i++) {
                    data[i] = data[i + 1]
                }
            } else {
                for (let i = evt.oldIndex; i > evt.newIndex; i--) {
                    data[i] = data[i - 1]
                }
            }
            data[evt.newIndex] = item
            vnode.componentInstance.$emit('input', data)
            vnode.context.$buefy.toast.open(`Moved ${item} from row ${evt.oldIndex + 1} to ${evt.newIndex + 1}`)
        }
    })
};

/**
 * We add a new instance of Sortable when the element
 * is bound or updated, and destroy it when it's unbound.
 */
const sortable = {
    name: 'sortable',
    bind(el, binding, vnode) {
        const container = el.querySelector('.process-editor')
        container._sortable = createSortable(container, binding.value, vnode)
    },
    update(el, binding, vnode) {
        const container = el.querySelector('.process-editor')
        container._sortable.destroy()
        container._sortable = createSortable(container, binding.value, vnode)
    },
    unbind(el) {
        const container = el.querySelector('.process-editor')
        container._sortable.destroy()
    }
}
Vue.directive('autoresize', {

    inserted: function (el) {

        el.style.height= el.scrollHeight + 'px'

        el.style.overflow.y = 'hidden'

        el.style.resize= 'none'

        function OnInput(){

            this.style.height = 'auto';

            this.style.height = (this.scrollHeight) + 'px';

            this.scrollTop = this.scrollHeight;

            window.scrollTo(window.scrollLeft,(this.scrollTop+this.scrollHeight));


        }

        el.addEventListener("input", OnInput, false);

    }

})
const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
    inline_styles: false,
    directives:{ sortable },
    formats: {
        underline: { inline: 'u', exact : true }
    },
    // OR register locally
    components: { Verte }
});

global.vm = app;

$("#sidebar_process_status").click(function (e) {
    e.preventDefault();
});

$("#sidebar_process_status").change(function () {
    $(this).closest('form').submit();
});

$("#sidebar_report_dropdown").click(function () {
    $(".child_2").toggle();
});

$("#sidebar_report_dropdown_3").click(function () {
    $(".child_3").toggle();
});

$(document).ready(function () {
    if (window.location.href.indexOf("#") > -1) {
        $('body').css('overflow', 'auto');
    }
    else
    {
        $('#blackboard-content-div').css('height', $(window).height() - 150);
        $('#blackboard-sidebar-div').css('height', $(window).height() - 150);
    }
});
/*
$( window ).resize(function() {
    $('#blackboard-content-div').css('height', $(window).height() - 150);
    $('#blackboard-sidebar-div').css('height', $(window).height() - 150);
});
*/
window.showScroller = function (elem) {
    if (window.location.href.indexOf("#") > -1) {
        //alert("Contains #");
    }
    else
    {
        $('#'+elem.id).css('overflow', 'auto');
    }
}

window.hideScroller = function (elem) {
    if (window.location.href.indexOf("#") > -1) {
        //alert("Contains #");
    }
    else
    {
        //$('#'+elem.id).css('overflow', 'hidden');
    }
}