<template>
    <div class="uploader-wrap" :id="id">
        <p class="action-buttons">
            <!-- <button class="upload-image" @click="wpMediaInit">Select Image</button> -->
            <button class="change-image" @click="wpMediaInit">Change Image</button>
            <button class="remove-image" @click="removeMedia">Remove</button>
        </p>
        <img :src="imgSrc" v-if="imgSrc !== ''"/>
    </div>
</template>

<script>
export default {
    name: 'uploader',
    model: {
        prop: 'imgValue',
        event: 'imgChange'
    },
    props: {
        id: {
            type: String
        },
        src: {
            type: String
        }
    },
    data() {
        return {
            imgSrc: this.src,
            // imageUploaded: false,
            
        }
    },
    methods: {
        wpMediaInit() {

            var frame, self = this
            
            if(frame) {
                frame.open()
                return
            }
            
            frame = wp.media({
                title: 'Select or Upload Media Of Your Chosen Persuasion',
                button: {
                    text : 'Use this media'
                },
                multiple: false
            })

            // console.log(frame)
        
            frame.open()

            frame.on('select', function() {
                var imgObj = frame.state().get('selection').toJSON()

                self.imgSrc = imgObj[0].url

                console.log(self.imgSrc)

                self.$emit('imgChange', self.imgSrc)
            })
        },
        removeMedia() {
            // this.imageUploaded = false
            this.imgSrc = ''

            this.$emit('imgChange', '')
        }
    }
}
</script>

<style lang="scss" scoped>
.action-buttons {
    button {
        padding-left: 25px;
        padding-right: 25px;
        min-height: 40px;
        border-radius: 20px;
        color: #fff;
    }
}

.upload-image, .change-image {
    background: -webkit-linear-gradient(left, #5b86e5, #36d1dc); /* For Safari 5.1 to 6.0 */
    background: -o-linear-gradient(right, #5b86e5, #36d1dc); /* For Opera 11.1 to 12.0 */
    background: -moz-linear-gradient(right, #5b86e5, #36d1dc); /* For Firefox 3.6 to 15 */
    background: linear-gradient(to right, #5b86e5, #36d1dc);
}

.remove-image {
    background: -webkit-linear-gradient(left, #ef629f, #eecda3); /* For Safari 5.1 to 6.0 */
    background: -o-linear-gradient(right, #ef629f, #eecda3); /* For Opera 11.1 to 12.0 */
    background: -moz-linear-gradient(right, #ef629f, #eecda3); /* For Firefox 3.6 to 15 */
    background: linear-gradient(to right, #ef629f, #eecda3);
}
</style>