export default {
	data () {
		return {
			items: []
		}
	},
	methods: {
		add(reply) {
			this.items.push(reply);
			this.$emit('added');
			flash('Reply was added');
		},
		remove(index) {
			this.items.splice(index, 1);
			this.$emit('removed');
			flash('Reply was deleted');
		}
	}
}