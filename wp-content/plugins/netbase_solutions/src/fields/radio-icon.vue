<template>
    <ul class="setting-radio-icon" :id="id">
        <li v-for="(option, index) in options" :key="index">
            <input
                :id="_uid + '__' + index"
                :name="name + '_radio-icon'"
                type="radio"
                :value="option"
                @change="updateIcon(option)"
                :checked="modelValue === option"
            />
            <label :for="_uid + '__' + index">
                <i :class="option"></i>
            </label>
        </li>
    </ul>
</template>

<script>
export default {
    name: 'radio-icon',
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
        modelValue: {
            type: String
        }
        // value: {
        //     type: String,
        // }
    },
    methods: {
        updateIcon(option) {
            this.$emit('change', option)
        }
    }
}
</script>

<style lang="scss" scoped>
.setting-radio-icon {
    display: flex;
    flex-wrap: wrap;
    li {
        margin-right: 10px;                
    }
    input[type=radio] {
        display: none;
        &:checked {
            & ~ label {
                background-color: #5b86e5;
                color: #fff;
                transform: scale(1.02);
                box-shadow: 0px 3px 8px 2px #ccc;
            }
        }
    }
    label {
        background-color: #eee;
        transition: all .3s ease;
        display: block;
        padding: 12px;
        border-radius: 50%;
        color: #444444;
        i {
            font-size: 20px;
        }
    }
}
</style>