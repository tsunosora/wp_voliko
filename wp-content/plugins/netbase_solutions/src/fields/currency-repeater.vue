<template>
  <table class="setting-currency-repeater" :id="id">
      <thead>
          <tr>
              <th>No.</th>
              <th v-for="(header, index) in headers" :key="index">
                  <span>{{header}}</span>
              </th>
          </tr>
      </thead>
      <tbody>
          <tr v-for="(field, index) in resultItems" :key="index">
              <td>{{index + 1}}</td>
              <!-- <td v-for="(field, key, index) in fields" :key="index">
                    <v-select
                        v-if="field.type === 'select'"
                        :label="'Select ' + field.name"                        
                        v-bind:items="objectToArray(field.options)"
                        :item-value="fieldItems"
                        single-line
                        bottom
                        autocomplete
                    ></v-select>
              </td> -->
              <td>
                  <v-select
                    :label="currency[field['nbt_currency-switcher_repeater_currency']] ? currency[field['nbt_currency-switcher_repeater_currency']] : 'Select Currency'"                        
                    v-bind:items="objectToArray(currency)"
                    :item-value="field['nbt_currency-switcher_repeater_currency']"
                    v-model="field['nbt_currency-switcher_repeater_currency']"
                    single-line
                    bottom
                    autocomplete
                  ></v-select>
              </td>
              <td>
                  <v-select
                    :label="field.position ? field.position : 'Select Position'"                        
                    v-bind:items="objectToArray(position)"
                    v-model="field['nbt_currency-switcher_position']"
                    single-line
                    bottom
                    autocomplete
                  ></v-select>
              </td>
              <td>
                  <v-select
                    :label="field.decimals ? field.decimals : 'Select Decimals'"
                    v-bind:items="objectToArray(decimals)"
                    v-model="field['nbt_currency-switcher_decimals']"                    
                    single-line
                    bottom
                    autocomplete
                  ></v-select>
              </td>
              <!-- <td>
                  <
              </td> -->
          </tr>
      </tbody>
  </table>
  <!-- <div>
      <div v-for="(field, key, index) in fields" :key="index">
        <v-select
            v-if="field.type === 'select'"
            :label="'Select ' + field.name"
            v-bind:items="objectToArray(field.options)"
            single-line
            bottom
            autocomplete
        ></v-select>
      </div>
  </div> -->

    <!-- <v-data-table
        v-bind:headers="headers"
        :items="objectToArray(resultItems)"
        hide-actions
        class="elevation-1"
    >
    <template slot="items" scope="props">
      <td v-for="(field, key, index) in fields" :key="index">
          <v-select
            v-if="field.type === 'select'"
            v-bind:items="objectToArray(field.options)"
            single-line
            bottom
          ></v-select>
          <div v-else-if="field.type === 'rate'">
              <input type="text" />
              <button>
                  <v-icon>refresh</v-icon>
              </button>
          </div>
      </td>
    </template>
  </v-data-table> -->
  <!-- <div>
  <v-select
    v-for="(field, key, index) in fields"
    :key="index"
    v-bind:items="objectToArray(field.options)"
    label="Select"
    single-line
    bottom
  ></v-select>
  </div> -->
</template>

<script>
// import ld from 'lodash'

export default {
    name: 'currency-repeater',
    props: {
        id: {
            type: String
        },
        fields: {
            type: Array | Object
        },
        resultItems: {
            type: Array | Object,
        },
    },
    data() {
        return {
            currency: this.fields.currency.options,
            position: this.fields.position.options,
            decimals: this.fields.decimals.options,
            headers: [],
            // items: [],
            positions: ['left', 'right'],
            decimals: [0,1,2,3,4,5,6,7,8,9],
            country: [],
            flagImg: '',
            fieldCount: 1,
            imageUploaded: false,            
        }
    },
    created() {
        console.log(this.resultItems)
        Object.values(this.fields).forEach(value => {
            this.headers.push(value.name)
        })
    },
    methods: {
        objectToArray(obj) {
            let arr = [];

            for (var key in obj) {
                if (obj.hasOwnProperty(key)) {
                    arr.push(obj[key]);
                }
            }

            return arr;
        }
    }    
}    
</script>

<style lang="scss">
.setting-currency-repeater {
    border-radius: 2px;
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;
    max-width: 100%;
    thead {
        background: #5b86e5;
        color: #fff;
        th {
            text-align: left;
            padding: 15px;
        }
    }
    tbody {
        td {
            padding-left: 15px;
            padding-right: 15px;
        }
    }
    .input-group--text-field {
        label {
            font-size: 14px;
            padding-left: 20px;
            top: 26px;
            color: #abc0d1 !important;
            font-weight: 400;
        }
    }
    .input-group {
        &__input {
            border: 1px solid #e2e2e2;
            border-radius: 25px;
            min-height: 45px;
            align-items: center;
            padding-left: 10px;
            .icon {
                margin-right: 15px;
                border-radius: 50%;
                color: #fff !important;
                background: -webkit-linear-gradient(left, #5b86e5, #36d1dc); /* For Safari 5.1 to 6.0 */
                background: -o-linear-gradient(right, #5b86e5, #36d1dc); /* For Opera 11.1 to 12.0 */
                background: -moz-linear-gradient(right, #5b86e5, #36d1dc); /* For Firefox 3.6 to 15 */
                background: linear-gradient(to right, #5b86e5, #36d1dc);
                box-shadow: rgba(0,0,0,0.15) 0px 2px 5px 0px;
                padding: 2px;
            }
        }
        &__selections__comma {
            padding-top: 12px;
            font-size: 14px;
        }
    }
    .input-group__details {
        min-height: 0;
        &:before {
            display: none;
        }
        &:after {
            display: none;
        }
    }
    .input-group__selections__comma {
        padding-left: 20px;
        color: #abc0d1;
        text-transform: capitalize;
    }
}
</style>