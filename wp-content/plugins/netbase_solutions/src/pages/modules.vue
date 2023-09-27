<template>
    <div class="modules-container-wrap">
        <transition name="fade">
            <AppLoader v-if="!loading"></AppLoader>
        </transition>

        <transition name="fade-slow">
            <v-container grid-list-xl v-show="loading">
                <v-layout row wrap> 
                    <v-flex xs12 md6 lg3 v-for="(module, value, index) in listActived" :key="index">
                        <div class="module-wrap">
                            <h2 class="module-name">{{module.label}}</h2>
                            <v-switch v-model="activatedModules" :value="module.name" @change="initGet = false"></v-switch>
                            <!-- <v-switch :label="module.label" v-model="activatedModules" :value="value" @change="initGet = false"></v-switch> -->
                            <p class="module-desc">{{module.description}}</p>
                        </div>
                    </v-flex>
                </v-layout>
                <div>
                    <v-btn
                        class="white--text modules-post app-button"
                        large
                        :disabled="buttonDisable"
                        @click="modifySettings"
                        :loading="buttonLoading"
                    >Save Settings</v-btn>
                </div>
            </v-container>            
        </transition>

        <v-snackbar
            :timeout=500000
            bottom
            right
            info
            v-model="showSnack"
            class="app-snackbar"
        >
            {{snackText}}
            <v-btn text @click.native="showSnack = false" class="white--text">
                <v-icon>cancel</v-icon>
            </v-btn>
        </v-snackbar>
    </div>
</template>

<script>
import Axios from 'axios'
import AppLoader from '../components/app-loader.vue'

export default {
    name: 'modules',
    data() {
        return {
            activatedModules: [],
            listActived: [],
            list: [],
            errors:[],
            loading: false,
            buttonLoading: false,
            buttonDisable: true,
            initGet: true,
            showSnack: false,
            snackText: '',
            parentData: []
        }
    },
    components: {
        AppLoader
    },
    created() {
        Axios.get(nb.api_route + 'modules/')
            .then(response => {
                this.activatedModules = response.data.activated_modules   
                this.list = response.data.list

                for(var module in response.data.list){
                    if(response.data.list[module].hide !== true){
                        this.listActived.push(response.data.list[module])
                    }
                }

                // console.log(this.listActived);

                this.loading = true
            })
            .catch(e => {
                this.errors.push(e)
            })
    },
    methods: {
        modifySettings() {
            this.buttonLoading = true

            console.log(nb.nonce);

            Axios.post(nb.api_route + 'modules/', {                
                modules_list: this.activatedModules
            })
            .then(res => {
                this.buttonLoading = false
                this.snackText = res.data.message
                this.showSnack = true

                console.log(res)
            })
            .catch(err => {
                console.log(err);
                this.snackText = 'There was some errors'
            })
        }
    },
    watch: {
        activatedModules: function(val, oldVal) {
            if(!this.initGet) {
                this.buttonDisable = false
            }
        }
    }
}
</script>

<style lang="scss">
.fade-enter-active,
.fade-leave-active
{
  transition: opacity .3s
}

.fade-enter,
.fade-leave-to
{
  opacity: 0
}

.fade-slow-enter-active,
.fade-slow-leave-active
{
  transition: opacity .9s
}

.fade-slow-enter,
.fade-slow-leave-to
{
  opacity: 0
} 

.dashboard-spinner {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.module-wrap {
    background: #fff;
    padding: 30px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    min-height: 220px;
    box-shadow: 3px 3px 10px 0 #ccc;
    -moz-box-shadow: 3px 3px 10px 0 #ccc;
    -webkit-box-shadow: 3px 3px 10px 0 #ccc;
    .module-name {
        flex: 0 0 calc(100% - 46px);
        max-width: calc(100% - 46px);
        margin: 0;
        font-size: 25px;
        color: #5b86e5;
        padding-top: 5px;
    }
    .switch {
        padding: 0;
        flex: 0 0 40px;
        max-width: 40px;
    }
    .module-desc {
        font-size: 14px;
        color: #abc0d1;
        margin-bottom: 0;
        margin-top: -15px;
    }
    .input-group.input-group--selection-controls.switch .input-group--selection-controls__ripple--active:after {
        background: -webkit-linear-gradient(left, #5b86e5, #36d1dc); /* For Safari 5.1 to 6.0 */
        background: -o-linear-gradient(right, #5b86e5, #36d1dc); /* For Opera 11.1 to 12.0 */
        background: -moz-linear-gradient(right, #5b86e5, #36d1dc); /* For Firefox 3.6 to 15 */
        background: linear-gradient(to right, #5b86e5, #36d1dc);
    }
    .input-group.input-group--selection-controls.switch .input-group--selection-controls__container {
        color: #adc2f2;
    }
}

.modules-post {
    margin-left: 15px !important;
    margin-top: 30px;
}

// .app-snackbar {
//     .snack__content {
//         height: 60px;
//         p {
//             font-size: 18px;
//             margin: 0;
//         }
//     }
//     .btn {
//         i {
//             color: #fff;
//         }
//         &:hover, &:focus {
//             .btn__content:before {
//                 background-color: transparent;
//             }
//         }
//     }
// }

</style>