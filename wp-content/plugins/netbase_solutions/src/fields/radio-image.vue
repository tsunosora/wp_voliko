<template>
    <ul class="setting-radio-image" :id="id">
        <li v-for="(option, index) in options" :key="index">
            <input
                :id="_uid + '__' + index"
                :name="_uid + '_radio-image'"
                type="radio"
                :value="option"
                @change="updateImage(option.name)"
                :checked="modelValue === option.name"
            />
            <label :for="_uid + '__' + index">
                <img :src="option.src" />
            </label>
        </li>
    </ul>
</template>

<script>
export default {
    name: 'radio-image',
    model: {
        prop: 'modelValue',
        event: 'change'
    },
    props: {
        id: {
            type: String
        },
        name: {
            type: String,
        },
        options: {
            type: Array | Object,            
        },
        // value: {
        //     type: String,
        // },
        modelValue: {
            type: String
        }
    },
    methods: {
        updateImage(option) {
            this.$emit('change', option)
            // console.log(this.value)
            
            // console.log(option)
        }
    }
}
</script>

<style lang="scss" scoped>
.setting-radio-image {
    display: flex;
    li {
        margin-right: 30px; 
        max-width: 150px;
        img {
            max-width: 100%;
        }     
    }
    label {
        opacity: .5;
        transition: all .3s ease;
    }
    input[type=radio] {
        display: none;
        &:checked {
            & ~ label {
                opacity: 1;
            }
        }
    }
}
</style>