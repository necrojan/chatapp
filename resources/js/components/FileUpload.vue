<template>
    <div>
        <i class="icon-picture pl-2" @click="pickFile"></i>
        <form enctype="multipart/form-data" class="d-none">
            <input type="file" ref="inputFile" @change="onFileUpload" multiple>
        </form>
        <div v-if="fileSaving" class="file-saving">
            <div class="file-saving__wrapper">
                <h3 class="mb-2">Uploading Files... {{ uploadPercentage }} %</h3>
                <b-progress :value="uploadPercentage" :max="max" variant="success"></b-progress>
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2';

    const FILE_SAVING = 1;
    const FILE_SUCCESS = 2;
    const FILE_ERROR = 3;

    export default {
        props: ['activeFriend'],
        data () {
            return {
                files: [],
                currentStatus: null,
                uploadPercentage: 0,
                max: 100
            }
        },

        computed: {
            fileSaving() {
                return this.currentStatus === FILE_SAVING;
            }
        },

        methods: {
            pickFile() {
                this.$refs.inputFile.click();
            },

            async onFileUpload(e) {
                const files = e.target.files;
                const formData = new FormData();

                if (!files.length) return;

                try {
                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        onUploadProgress: (progressEvent) => {
                            this.uploadPercentage = Math.round(progressEvent.loaded * 100 / progressEvent.total);
                        }
                    };
                    for (let i = 0; i < files.length; i++) {
                        formData.append('attachments[]', files[i]);
                    }
                    this.currentStatus = FILE_SAVING;
                    const upload = await axios.post('/api/file-upload', formData, config);

                    if (upload.data.success) {
                        this.currentStatus = FILE_SUCCESS;
                        for (let i = 0; i < files.length; i++) {
                            const success = await axios.post('/api/private', {
                                receiver_id: this.activeFriend,
                                message: files[i].name
                            });
                            if (success) this.$emit('imagename', success.data.message);
                        }

                        await Swal.fire({
                            title: 'Success!',
                            text: 'File was uploaded!',
                            icon: 'success',
                            showConfirmButton: false
                        });
                    }
                } catch (e) {
                    this.currentStatus = FILE_ERROR;
                    await Swal.fire({
                        title: 'Oops!',
                        text: 'File(s) cannot be uploaded!',
                        icon: 'error',
                        showConfirmButton: false
                    });
                }
            }
        }
    }
</script>
