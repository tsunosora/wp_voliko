<template>
	<v-container class="module-settings-wrap">
		<transition name="fade">
			<AppLoader v-if="!loading"></AppLoader>
		</transition>
		<transition name="fade-slow">
			<v-layout row wrap v-if="loading"> 
				<v-flex class="module-settings" xs12 v-for="(module, name) in modules" :key="name">
					<div class="setting-wrap">
						<router-link class="module" :to="`/settings/${module.slug}`">
							<span class="col10">
								{{module.module_name}}
							</span>
							<span class="col2">
								<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22"><defs><clipPath><path fill="#00f" fill-opacity=".514" d="m-7 1024.36h34v34h-34z"/></clipPath><clipPath><path fill="#aade87" fill-opacity=".472" d="m-6 1028.36h32v32h-32z"/></clipPath></defs><path d="m345.44 248.29l-194.29 194.28c-12.359 12.365-32.397 12.365-44.75 0-12.354-12.354-12.354-32.391 0-44.744l171.91-171.91-171.91-171.9c-12.354-12.359-12.354-32.394 0-44.748 12.354-12.359 32.391-12.359 44.75 0l194.29 194.28c6.177 6.18 9.262 14.271 9.262 22.366 0 8.099-3.091 16.196-9.267 22.373" transform="matrix(.03541-.00013.00013.03541 2.98 3.02)" fill="#4d4d4d"/></svg>
							</span>
						</router-link>
					</div>
					<v-snackbar
						:timeout=5000
						bottom
						right
						vertical
						multi-line
						v-model="showSnack"
						class="app-snackbar"
					>
						<p>{{snackText}}</p>
						<v-btn flat @click.native="showSnack = false" class="white--text">
							<v-icon>cancel</v-icon>
						</v-btn>
					</v-snackbar>
				</v-flex>
			</v-layout>
		</transition>
	</v-container>
</template>

<script>
	// import ld from 'lodash'
	import Axios from 'axios'
	import Values from 'lodash.values'
	import ColorPicker from '../fields/color-picker.vue'
	import RadioIcon from '../fields/radio-icon.vue'
	import Uploader from '../fields/uploader.vue'
	// import CurrencyRepeater from '../fields/currency-repeater.vue'
	import RadioImage from '../fields/radio-image.vue'
	// import SettingFields from '../components/setting-fields.vue'
	import AppLoader from '../components/app-loader.vue'
	import { setTimeout } from 'timers';

	export default {
		name: 'settings',
		data() {
			return {
				modules: [],
				loading: false,
				snackText: '',
				showSnack: false,
				buttonLoading: false,
			}
		},
		components: {
			// SettingFields
			ColorPicker,
			RadioIcon,
			Uploader,
			//CurrencyRepeater,
			RadioImage,
			AppLoader,
		},
		created() {
			Axios.get(nb.api_route + 'settings/')
				.then(response => {
					this.modules = response.data;
					this.loading = true
				})
				.catch(e => {
					console.log(e)
				})
		}
	}
</script>

<style lang="scss">
	@import '../scss/variable.scss';

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

	.module-settings-wrap {
		// margin-left: -700px;
		// padding-left: 700px;
		padding: 0px;
		ul {
			padding-left: 0
		}
		button {
			margin-bottom: 0;
		}
		.layout {
			border: 1px solid #ccc;
			box-shadow: 0px 0px 10px #ccc;
			-moz-box-shadow: 0px 0px 10px #ccc;
			-webkit-box-shadow: 0px 0px 10px #ccc;
			border-radius: 5px;
			overflow: hidden;
			margin-right: 0px;
			margin-left: 0px;
		}
		.module-settings{
			padding: 0px;
			margin-bottom: 0px;
			display: flex;
			width: 100%;
			min-height: 75px;
			&:not(:last-child){
				border-bottom: 1px solid #ccc;
			}
		}
		.setting-number-wrap {
			max-width: 240px;
			.slider {
				padding-top: 0;
				padding-bottom: 0;
			}
		}
		.setting-wrap{
			padding: 15px 15px 15px 25px;
			width: 100%;
			.module {
				display: flex;
				font-size: 16px;
				font-weight: 600;
				color: #5b86e5;
				height: 100%;
				.col10{
					width: calc(100% - 40px);
					float: left;
					height: fit-content;
					margin: auto;
				}
				.col2{
					width: 32px;
					float: left;
					height: 32px;
					margin: auto;
					margin-right: -5px;
				}
			}
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