<template>
	<div>
		<h1>{{ currency.currency }} ({{ currency.abbreviation }})</h1>
		<br>

		<rates-component :id="id"></rates-component>
	</div>
</template>

<script>
import RatesComponent from './RatesComponent';

export default {
	data: () => ({
		id: null,
		valid: false,
		currency: {
			abbreviation: '',
			currency: '',
			id: '',
			sign: '',
			_method: 'PUT'
		}
	}),
	
	components: {RatesComponent},

	watch: {
		'$route.params.id': function(id) {
			this.update();
		}
	},

	created() {
		this.update();
	},

	methods: {
		// update view data
		update() {
			this.id = this.$route.params.id;

			this.loadCurrency();
		},
		// load currency information
		loadCurrency() {
			axios.get('/api/currencies/' + this.id)
			.then(res => {
				if (res.status == 200) {
					this.currency = res.data.data;
				}
				else {
					console.log(res);
				}
			})
			.catch(error => console.warn(error));
		}
	}
}
</script>