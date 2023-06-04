<template>
<div class="mt-4">
    <div>
        <h3 class="d-block">Financial Advisor Information </h3><FADetailsEdit class="float-right" v-if="isFinancialAdvisor"></FADetailsEdit><br><br>
    </div>
    <div class="row">
        <div class=" col-md-4" v-for="(section, index) in sections" :key="index">
            <div class="section elevation-1" :class="section.open?'section-name-click':''">
                <div class="section-name"  @click="toggleOpen(index)">
                    <h4>{{section.name}}</h4>
                </div>
                <div class="advisor-details">
                    <div class="mb-0" v-for="(input, i) in section.inputs" :key="i">
                        <div class="row">
                            <p class="col-md-6 text-bold">{{input.label}}</p>
                            <p class="col-md-6" v-if='input.data !== ""'>: {{input.data}}</p>
                            <p class="col-md-6" v-if='input.data === ""'>: <small><em>There's no data captured</em></small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
import FADetailsEdit from "./FADetailsEdit";

export default {
name: "FADetailsShow",
    props: ["isFinancialAdvisor"],
    components: {
        FADetailsEdit
    },
    data: () => {
        return {
            sections: [],
        }
    },
    mounted() {
        axios.get('/fa-details/load?edit=true')
            .then(response =>{
                this.sections = response.data.forms;
            })
            .catch(response =>{
                console.log(response)
            })
    },
    methods: {
        toggleOpen(index){
            this.sections.map((section, i) =>{
                if(index === i){
                    section.open = !section.open;
                }else{
                    section.open = false;
                }
                return section;
            });
        }
    }
}
</script>

<style scoped>
    .section{
        background: #eefafd;
        padding: 15px;
    }
    .section-name{
        position: relative;
        transition: all 0.4s linear;
        cursor: pointer;
    }
    .section-name::after{
        content: "";
        font-weight: bolder;
        position: absolute;
        top: 50%;
        right: 0;
        transform: translateY(-50%) rotate(0deg);
        width: 30px;
        height: 30px;
        transition: all 0.2s linear;
        background-image: url("../../../../../public/img/arrow-down-mint.svg");
        background-position: center;
        background-size: contain;
        background-repeat: no-repeat;
    }

    .section-name-click .section-name::after{
        transform: translateY(-50%) rotate(90deg);
    }

    .section-name-click .section-name{
        margin-bottom: 15px;
    }
    .advisor-details{
        opacity: 0;
        max-height: 0;
        overflow-y: hidden;
        transition: all 0.4s easy-out;
        background: #f8f8f8;
        padding: 8px;
    }

    .section-name-click .advisor-details{
        opacity: 1;
        max-height: 1000px;
    }

</style>