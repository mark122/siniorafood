<template>
    <div class="overflow-y-auto h-screen px-2">
        <div class="bg-gray-100 py-4 lg:py-4 rounded">
            <div class="container px-6 mx-auto flex flex-col md:flex-row items-start md:items-center justify-between">
                <div>
                    <h4 class="text-base font-semibold leading-tight text-gray-800">
                        {{ title }} - {{currentId}}
                    </h4>
                </div>
            </div>
        </div>
        <div v-if="loading" class="my-6 w-11/12 mx-auto xl:w-full xl:mx-0">
            <form-input-shimmer></form-input-shimmer>
            <form-input-shimmer></form-input-shimmer>
            <form-input-shimmer></form-input-shimmer>
            <form-switch-shimmer></form-switch-shimmer>
        </div>
        <form v-else class="my-6 w-11/12 mx-auto xl:w-full xl:mx-0" @submit.prevent="submitForm">
            <div class="w-full flex flex-col mb-6">
                <label for="title" class="pb-2 font-semibold text-gray-800">Title</label>
                <InputText type="text"
                           id="title"
                           v-model="form.title"
                           placeholder="Document Title" aria-describedby="title-help"
                           :class="[errors.title ? 'p-invalid' : '']"

                />
                <small id="title-help" v-if="errors.title" class="p-invalid">{{ errors.title }}</small>
            </div>

            <!-- Hero Image Path -->
            <div class="col-span-6 sm:col-span-4 mb-6">

                <arc-label for="image" value="Douments" />

                <input type="file" 
                       ref="image"
                       @change="updateImagePreview">

                <arc-secondary-button class="mt-2 mr-2" style="display:none" type="button" @click.native.prevent="selectNewImage">
                    Select Douments
                </arc-secondary-button>
            </div>

            <div class="w-full flex">
                <Button type="submit" :label="editFlag ? 'Update' : 'Create'" />
            </div>
        </form>
    </div>
</template>
<script>
    import InputText from 'primevue/inputtext';
    import Button from 'primevue/button';
    import InputSwitch from 'primevue/inputswitch';
    import Textarea from 'primevue/textarea';
    import TextEditor from "@/Components/TextEditor";
    import Dropdown from 'primevue/dropdown';
    import ArcActionMessage from "@/Components/ActionMessage";
    import ArcButton from "@/Components/Button";
    import ArcFormSection from "@/Components/FormSection";
    import ArcInput from "@/Components/Input";
    import ArcTextArea from "@/Components/TextArea";
    import ArcInputError from "@/Components/InputError";
    import ArcLabel from "@/Components/Label";
    import ArcSecondaryButton from "@/Components/SecondaryButton";
    import FormInputShimmer from "@/Components/Shimmers/FormInputShimmer";
    import FormSwitchShimmer from "@/Components/Shimmers/FormSwitchShimmer";
    export default {
        name: 'DocumentUploadForm',
        components: {
            InputText,
            Button,
            InputSwitch,
            Textarea,
            TextEditor,
            FormInputShimmer,
            FormSwitchShimmer,
            ArcActionMessage,
            ArcLabel,
            Dropdown,
            ArcActionMessage,
            ArcButton,
            ArcFormSection,
            ArcInput,
            ArcTextArea,
            ArcInputError,
            ArcLabel,
            ArcSecondaryButton,
        },
        props: {
            editFlag: Boolean,
            currentId: Number,
            skillId: Number,
            sections: Array,
            errors: Object,
            title: ''
        },
        data() {
            return {
                form: {
                    title: '',
                    document_path: '',
                    user_id: this.currentId,
                },
                formValidated: false,
                loading: false,
                imagePreview: null,
            }
        },
        methods: {
            submitForm() {
                this.editFlag ? this.update() : this.create();
            },
            create() {
                this.formValidated = true;
                this.$inertia.post(route('userdocuments.store'), this.form, {
                    onSuccess: () => {
                        if (Object.keys(this.errors).length === 0) {
                            this.$emit('close', true);
                        }
                    },
                });
            },

            selectNewImage() {

                this.$refs.image.click();
            },

            updateImagePreview() {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };

                reader.readAsDataURL(this.$refs.image.files[0]);

                if (this.$refs.image) {
                    this.form.document_path = this.$refs.image.files[0]
                }
                ;

            },
        },
        mounted() {
            if(this.editFlag === true) {
                this.fetch();
            }
        }
    }
</script>
