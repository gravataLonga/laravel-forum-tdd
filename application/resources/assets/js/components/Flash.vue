<template>
    <div 
        class="alert alert-flash" 
        :class="'alert-'+level" 
        role="alert" 
        v-show="show" 
        v-text="body">
    </div>
</template>

<script>
    export default {
        props: ['message'],
        data () {
            return {
                body: this.message,
                level: 'success',
                show: false
            }
        },

        created() {
            this.flash(this.message)

            window.events.$on('flash', data => this.flash(data));
        },

        methods: {
            flash(data) {
                if (data.message == '') return;
                this.show = true;
                this.body = data.message;
                this.level = data.level;

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