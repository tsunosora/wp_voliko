<template>
	<v-container class="module-settings-field-wrap">
		<transition name="fade">
			<AppLoader v-if="!loading"></AppLoader>
		</transition>
		<transition name="fade-slow">
			<v-layout row wrap v-if="loading"> 
				<v-flex class="module-settings" xs12 v-if="moduleFields">
					<div class="setting-wrap">
						<h3 class="module-title">{{moduleFields.module_name}}</h3>                        
						<div class="setting-details" v-for="(s, key, index) in moduleFields.settings" :key="index">
							<h4 class="setting-title">{{s.name}}</h4>
							<p v-html="s.desc"></p>
							<div class="setting-color-picker-wrap" v-if="s.type === 'color'">
								<ColorPicker
									:id="s.id"
									:color="!s.value ? s.default : s.value"
									v-model="s.value"
								></ColorPicker>
							</div>
							<div class="setting-radio-icon-wrap" v-else-if="s.type === 'icon'">                                
								<RadioIcon
									:id="s.id"
									:name="moduleFields.slug"
									:options="s.options"
									:modelValue="s.value"
									v-model="s.value"
								></RadioIcon>
							</div>
							<div class="setting-radio-icon-wrap" v-else-if="s.type === 'radio_image'">                                
								<RadioImage
									:id="s.id"
									:name="s.name"
									:options="s.option"
									:modelValue="s.value"
									v-model="s.value"
								></RadioImage>
							</div>
							<div class="setting-radio-icon-wrap" v-else-if="s.type === 'image'">                                
								<Uploader
									:id="s.id"
									:src="s.value"
									v-model="s.value"
								></Uploader>
							</div>
							<div class="setting-number-wrap" v-else-if="s.type === 'number'">
								<v-slider
									:id="s.id"
									v-model="s.value"
									thumb-label
									v-bind:min="s.min"
									v-bind:max="s.max" 
									v-bind:step="s.step"
								></v-slider>
							</div>
							<div class="setting-checkbox-wrap" v-else-if="s.type === 'checkbox'">
								<v-switch
									:id="s.id"
									v-model="s.value"
									:label="s.label"
								></v-switch>
							</div>
							<div class="setting-text-wrap" v-else-if="s.type === 'textarea'">
								<textarea :id="s.id" class="app-textarea" v-model="s.value" :placeholder="s.value"></textarea>
							</div>
							<div class="setting-text-wrap" v-else-if="s.type === 'text'">
								<input
									class="app-textinput"
									:name="s.name"
									:id="s.id"
									type="text"
									v-model="s.value"
								/>
							</div>
							<div class="setting-text-wrap" v-else-if="s.type === 'select'">
								<!-- <v-select
									:items="objectToArray(s.options)"
									:item-text="s.value"
									label="Select"                                    
								></v-select> -->
								<v-select
									class="select-field"
									v-model="s.value"
									:items="objectToArray(s.options)"
									attach
									label="Select"
								></v-select>
							</div>
						</div>
						<v-btn
							class="white--text settings-post app-button"
							large
							@click="updateModuleSettings(moduleFields.slug, moduleFields.settings)"
							:loading="buttonLoading"
						>Save Settings</v-btn>
						<v-btn
							class="white--text settings-post app-button back"
							large
							:href="`#/settings/`"
						>Back</v-btn>
					</div>
					<v-snackbar
						:timeout=5000
						bottom
						right
						multi-line
						v-model="showSnack"
						class="app-snackbar"
					>
						<p>{{snackText}}</p>
						<v-btn text @click.native="showSnack = false" class="white--text">
							<v-icon>cancel</v-icon>
						</v-btn>
					</v-snackbar>
				</v-flex>
			</v-layout>
		</transition>
	</v-container>
</template>

<script>
	import Axios from 'axios'
	import Values from 'lodash.values'
	import ColorPicker from '../fields/color-picker.vue'
	import RadioIcon from '../fields/radio-icon.vue'
	import Uploader from '../fields/uploader.vue'
	import RadioImage from '../fields/radio-image.vue'
	import AppLoader from '../components/app-loader.vue'

	export default {
		name: 'settings',
		data() {
			return {
				loading: false,
				moduleFields: [],
				snackText: '',
				showSnack: false,
				buttonLoading: false,
			}
		},
		components: {
			ColorPicker,
			RadioIcon,
			Uploader,
			RadioImage,
			AppLoader,
		},
		created() {
			Axios.get(nb.api_route + 'settings/' + this.$route.params.module)
				.then(response => {
					this.moduleFields = response.data;
					this.loading = true;
				})
				.catch(e => {
					console.log(e)
				})
		},
		methods: {        
			updateModuleSettings(module, settings) {
				let settingsToPost = {}

				this.buttonLoading = true
				Object.values(settings).forEach(value => {
					settingsToPost[value.id] = value.value
				})

				Axios.post(nb.api_route + 'modules/' + module, {
					settings: settingsToPost
				})
				.then(response => {
					this.buttonLoading = false
					this.snackText = 'Save success'
					this.showSnack = true
					console.log(this.showSnack);
				})
				.catch(e => {
					console.log(e)
				})
			},
			objectToArray(obj) {
				let arr = [];

				for (let key in obj) {
					if (obj.hasOwnProperty(key)) {
						arr.push({
							value: key,
							text: obj[key]
						});
					}
				}

				return arr;
			},
			uploaderSrcChange(src) {
				console.log(src);
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

	.app-textarea {
		min-width: 260px;
		min-height: 100px;
		border: 1px solid #ddd;
		border-radius: 3px;
		@media (min-width: 500px) {
			min-width: 400px;
		}
	}

	.app-textinput {
		min-width: 260px;
		min-height: 40px;
		border-radius: 3px;
		@media (min-width: 500px) {
			min-width: 400px;
		}
	}

	.module-settings-field-wrap {
		// margin-left: -700px;
		// padding-left: 700px;
		ul {
			padding-left: 0
		}
		button {
			margin-bottom: 0;
			&.app-button{
				margin-right: 10px;
			}
		}
		.app-button{
			&.back{
				float:right;
			}
			@media (max-width: 500px) {
				width: 100%;
				margin-right: 0px;
				margin-top: 10px;
			}
		}
		.select-field{
			input{
				display: none;
			}
		}
		.setting-number-wrap {
			max-width: 240px;
			.slider {
				padding-top: 0;
				padding-bottom: 0;
			}
		}
		.setting-wrap {
			padding: 15px;
		}
	}

	.module-settings {
		background-color: #fff;
		padding: 30px;
		margin-bottom: 45px;
		.setting-details {
			margin-bottom: 45px;
		}
		.module-title {
			font-size: 20px;
			margin-top: 0;
			padding-bottom: 15px;
			margin-bottom: 30px;
			position: relative;
			color: #5b86e5;
				border-bottom: 1px solid #eee;
			&:before {
				content: '';
				height: 1px;
				width: 90px;
				background: #11ffbd;
				display: block;
				position: absolute;
				bottom: -1px;
			}
		}
		.setting-title {
			font-size: 16px;
			margin-top: 0;
		}    
	}
</style>