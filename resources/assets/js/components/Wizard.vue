<template>
    <div class="wizard-wrapper" v-if="is_dismissed">
        <div class="wizard-container">
            <!-- Left column &ndash -->
            <div id="connexion">
                <img src="/assets/xcell.png" alt="Blackboard Logo" id="wizard-logo">
                <p class="paragraphe">
                    Please capture your {{users_number}} remaining users or click skip to continue into the system.
                </p>
                <a href="#" class="btn-link" v-on:click="dismissWizard">Skip</a>
            </div>

            <!-- END OF LEFT COLUMN -->

            <div id="inscription">
                <!-- LOADER -->
                <div v-if="loading">
                    <div class="lds-hourglass"></div>
                    <p class="paragraphe">We are registering {{first_name}} for you.</p>
                </div>

                <!-- END OF LOADER &ndash -->
                <!-- USER REGISTRATION &ndash -->

                <div id="user-count" v-if="form">
                    <span class="wizard-user">{{users_number}}</span><br>
                    <strong>Users remain</strong>
                </div>
                <div class="alert alert-danger mt-3" v-if="is_error" role="alert">
                    <ul>
                        <li v-for="error in error_message">{{error}}</li>
                    </ul>
                </div>
                <form class="mt-5" v-if="form">
                    <div class="form-group row">
                        <label for="first_name" class="col-sm-5 col-form-label">First Name <span style="color: red">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" v-model="first_name" id="first_name" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="last_name" class="col-sm-5 col-form-label">Last Name <span style="color: red">*</span></label>
                        <div class="col-sm-7">
                            <input type="text" v-model="last_name" id="last_name" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-5 col-form-label">Email <span style="color: red">*</span></label>
                        <div class="col-sm-7">
                            <input type="email" id="email" v-model="email" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="region" class="col-sm-5 col-form-label">Region</label>
                        <div class="col-sm-7">
                            <select v-model="region_id" id="region" class="form-control form-control-sm">
                                <option disabled value="">Please Select Region</option>
                                <option v-for="region in regions" :key="region.id" :value="region.id">{{region.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="area" class="col-sm-5 col-form-label">Area</label>
                        <div class="col-sm-7">
                            <select v-model="area_id" id="area" class="form-control form-control-sm">
                                <option disabled value="">Please Select Area</option>
                                <option v-for="area in areas" :key="area.id" :value="area.id">{{area.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="office" class="col-sm-5 col-form-label">Office</label>
                        <div class="col-sm-7">
                            <select disabled v-model="office_id" id="office" class="form-control form-control-sm">
                                <option disabled value="">Please Select Office</option>
                                <option v-for="office in offices" :key="office.id" :value="office.id">{{office.name}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="group-form">
                        <input type="submit" class="inscription" value="Save" v-on:click.prevent="submitUser">
                    </div>
                </form>

                <!-- END OF USER REGISTRATION -->
                <!-- FA DETAILS -->

                <div class="text-left" v-if="capture_fa_details">
                    <h3>Please select the one you use</h3>
                    <div class="form-group form-check" v-for="broker in brokers">
                        <input type="checkbox" v-model="broker.id" class="form-check-input" :id="broker.id">
                        <label class="form-check-label" :for="broker.id">{{broker.name}}</label>
                    </div>
                    <div class="group-form">
                        <input type="submit" class="inscription" :value="fa_button_value" v-on:click.prevent="">
                    </div>
                </div>

                <!-- END FA DETAILS &ndash -->
                <!-- WIZARD COMPLETED ANIMATION &ndash -->

                <div v-if="completed">
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                is_dismissed: false,
                office_id: 0,
                users_number: 3,
                sub_users: 0,
                first_name: '',
                last_name: '',
                email: '',
                region_id: 0,
                area_id: 0,
                regions:[],
                areas:[],
                offices:[],
                loading: false,
                form: true,
                completed:false,
                is_error:false,
                error_message:[]
            }
        },
        mounted() {
            this.getDismissed()
        },
        methods: {
            getDismissed: function () {
                axios.get('/is-wizard-dismissed')
                    .then(response => {
                        this.is_dismissed = (response.data.dismiss == 0) ? true : false;
                        this.office_id = response.data.office_id;
                        this.regions = response.data.region_drop_down;
                        this.areas = response.data.area_drop_down;
                        this.offices = response.data.offices_drop_down;
                        this.sub_users = response.data.sub_users;
                        this.users_number -= response.data.default_user_count;
                        console.log(response.data.dismiss);
                    })
                    .catch(function (error) {
                        // handle error
                        //console.log(error);
                    })
            },
            dismissWizard: function () {
                axios.post('/dismiss-wizard', {
                    dismiss: 1,
                    office_id: this.office_id
                })
                    .then(response => {
                        this.is_dismissed = response.data.dismiss;
                        //console.log(response.data.dismiss);
                    })
                    .catch(function (error) {
                        //console.log(error);
                    });
            },
            submitUser: function () {
                if (!this.first_name || !this.last_name || !this.email){
                    this.error_message = [];
                    if (!this.first_name){
                        this.error_message.push("First Name is required");
                    }
                    if (!this.last_name){
                        this.error_message.push("Last Name is required");
                    }
                    if (!this.email){
                        this.error_message.push("Email is required");
                    }
                    this.is_error = true;
                    return false;
                }
                this.loading = true;
                this.form = false;
                this.is_error = false;
                axios.post('/users', {
                    first_name: this.first_name,
                    last_name: this.last_name,
                    email: this.email,
                    region: [this.region_id],
                    area: [this.area_id],
                    office: [this.office_id],
                    wizard: true,
                    role: [14]
                })
                    .then(response => {
                        if (response.data.user_count == this.sub_users){
                            /*this.loading = false;
                            this.completed = true;
                            setTimeout(() =>  this.is_dismissed = false , 2000);*/
                        }else{
                            this.first_name = '';
                            this.last_name = '';
                            this.email = '';
                            this.loading = false;
                            this.form = true;
                        }

                        this.users_number = this.sub_users - response.data.user_count;
                    })
                    .catch(error => {
                        this.form = true;
                        this.is_error = true;
                        this.loading = false;
                        this.error_message = error.response.data.errors;
                        console.log(error.response);
                    });
            }
        }
    }
</script>

<style scoped>
    .wizard-wrapper{
        background-color: rgba(249, 249, 249,.92);
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 9999!important;
    }
    .wizard-form-wrapper{
        width: 50%;
        background-color: #ffffff;
        height: 600px;
        position: absolute;
        top:0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
    }
    #wizard-logo{
        width: 100px;
        display: block;
        margin:0 auto 1rem auto;
    }
    header{
        padding: 0 2rem;
        text-align: center;
    }
    h3{
        text-align: center;
    }

    .wizard-container{
        display: flex;
        flex-direction: column;
        width: 60%;
        margin: 0 auto;
    }

    #connexion, #inscription{
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    #connexion{
        background: #ffffff;
        padding: 1.5rem;
    }

    .title{
        font-size: 2.7em;
        margin: 0;
    }
    .paragraphe{
        margin: 20px 0;
    }
    .paragraphe{
        opacity: 0.81;
        font-size: 18px;
    }
 input[type="submit"]{
        display: block;
        line-height: 40px;
        border: 1px solid  #4c5e77;
        text-align: center;
        border-radius: 9999px;
        padding-left: 3rem;
        padding-right: 3rem;
        color: #4c5e77;;
        font-size: 20px;
        font-weight: lighter;
        transition: .2s;
        cursor: pointer;
    }
    .btn-link{
        text-decoration: underline;
    }
    /*
        connexion
    */
    #inscription{
        background: #46C8EA;
        text-align: center;
    }

    #connexion .title{
        color: #4c5e77;
    }

    #connexion .paragraphe{
        color: #4c5e77;

    }
    #connexion .connexion:hover{
        background-color: #4c5e77;;
        color: #46C8EA;
    }
    /*
        inscription
    */

    #inscription .title{
        color: #4c5e77;
    }
    #inscription .inscription{
        background: #46C8EA;
        margin: 0 auto;
    }

    #inscription .inscription:hover{
        background: #fff;
        color: #46C8EA;
        border: 1px solid #46C8EA;
    }

    .lds-hourglass {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }
    .lds-hourglass:after {
        content: " ";
        display: block;
        border-radius: 50%;
        width: 0;
        height: 0;
        margin: 8px;
        box-sizing: border-box;
        border: 32px solid #fff;
        border-color: #fff transparent #fff transparent;
        animation: lds-hourglass 1.2s infinite;
    }
    @keyframes lds-hourglass {
        0% {
            transform: rotate(0);
            animation-timing-function: cubic-bezier(0.55, 0.055, 0.675, 0.19);
        }
        50% {
            transform: rotate(900deg);
            animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
        }
        100% {
            transform: rotate(1800deg);
        }
    }
    .wizard-user{
        font-size: 5.5rem;
        line-height: 0.7;
    }

    #user-count{
        border: 1px solid #4c5e77;
        border-radius: 50%;
        padding: 2rem;
    }

    .checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #4c5e77;
        fill: none;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }

    .checkmark {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: block;
        stroke-width: 2;
        stroke: #fff;
        stroke-miterlimit: 10;
        margin: 10% auto;
        box-shadow: inset 0px 0px 0px #4c5e77;
        animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    }

    .checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }

    @keyframes stroke {
        100% {
            stroke-dashoffset: 0;
        }
    }
    @keyframes scale {
        0%, 100% {
            transform: none;
        }
        50% {
            transform: scale3d(1.1, 1.1, 1);
        }
    }
    @keyframes fill {
        100% {
            box-shadow: inset 0px 0px 0px 50px #4c5e77;
        }
    }
    @media (min-width: 992px){
        .wizard-container{
            flex-direction: row;
        }

        #connexion{
            width: 45%;
            text-align: center;
        }

        #inscription{
            width: 55%;
        }
    }
</style>