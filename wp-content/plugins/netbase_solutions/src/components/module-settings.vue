<template>
	<div class="module-settings-wrap">
		<!-- <h2 class="module-name">{{moduleName}}</h2> -->
		<div class="module-settings" v-for="(setting, index) in settings" :key="index">
			<div class="setting-wrap">
				<h3 class="module-title">{{setting.module_name}}</h3>
				<div class="setting-details" v-for="(s, i) in setting.settings" :key="i">
					<h4 class="setting-title">{{s.name}}</h4>
					<div class="setting-color-picker-wrap" v-if="s.type === 'color'">
						<ColorPicker :color="s.value"></ColorPicker>
					</div>
					<div class="setting-radio-swatch-wrap" v-else-if="s.type === 'icon' || s.type === 'radio_image'">
						<RadioSwatch :name="setting.slug" :type="s.type" :options="s.options"></RadioSwatch>
					</div>
					<div class="setting-number-wrap" v-else-if="s.type === 'number'">
						<v-card-text>
							<v-slider
								v-model="s.default"
								thumb-label
								step="10"
							></v-slider>
						</v-card-text>
					</div>
					<div class="setting-text-wrap" v-else>
						<input type="text" :value="s.value"/>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import ColorPicker from '../fields/color-picker.vue'
	import RadioSwatch from '../fields/radio-swatch.vue'

	export default {
		name: 'module-settings',
		props: {        
			settings: {},
		},
		components: {
			ColorPicker,
			RadioSwatch
		},
		created() {
			// this.settings.forEach(function(e) {
			//     console.log(e.module_name)
			// })

			// for(var e in this.settings) {
			//     console.log(e.module_name)
			// }
			console.log(this.settings)
		}
	}
</script>

<style>

</style>