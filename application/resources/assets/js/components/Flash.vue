<template>
    <div class="alert alert-success alert-flash" role="alert" v-show="show">
        <strong>Success!</strong> {{ body }}
    </div>
</template>

<script>
    export default {
        props: ['message'],
        data () {
            return {
                body: this.message,
                show: false
            }
        },

        created() {
            this.flash(this.message)

            window.events.$on('flash', message => this.flash(message));
        },

        methods: {
            flash(message) {
                if (message == '') return;
                this.show = true;
                this.body = message;

                this.hide();
            },

            hide() {
                setTimeout(() => {
                    this.show = false;
                }, 3000);
            }
        }
    }
</script>

<style>
    .alert-flash {
        position: fixed;
        bottom: 25px;
        right: 25px;
    }
</style>