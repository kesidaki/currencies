<template>
	<div>
		<h1>{{ currency.currency }} ({{ currency.abbreviation }})</h1>
		<br>

		<v-form ref="form" v-model="valid" lazy-validation>
	        <v-alert :value="true" type="success" v-if="stored">Currency Added</v-alert>

	        <v-alert :value="true" type="error" v-if="errors.abbreviation.status">{{ errors.abbreviation.text }}</v-alert>
	        <v-alert :value="true" type="error" v-if="errors.currency.status">{{ errors.currency.text }}</v-alert>

	        <v-layout wrap>
	            <v-flex xs12>
	                <v-text-field
	                v-model="currency.currency"
	                :rules="nameRules"
	                :counter="25"
	                label="Currency"
	                required
	                ></v-text-field>
	            </v-flex>

	            <v-flex xs12 md4>
	                <v-text-field
	                v-model="currency.abbreviation"
	                :rules="codeRules"
	                :counter="4"
	                label="Abbreviation"
	                required
	                ></v-text-field>
	            </v-flex>

	            <v-flex xs12 md8>
	                <v-text-field
	                v-model="currency.sign"
	                :rules="symbolRules"
	                :counter="8"
	                label="Sign / Symbol"
	                ></v-text-field>
	            </v-flex>

	            <v-flex xs12>
	                <v-btn
	                color="blue white--text"
	                :disabled="!valid"
	                @click="updateCurrency">Update</v-btn>
	            </v-flex>
	        </v-layout>
	    </v-form>

		<rates-component :id="id"></rates-component>
	</div>
</template>

<script>
import RatesComponent from './RatesComponent';

export default {
	data: () => ({
		id: null,
		valid: false,
        stored: false,
		currency: {
			abbreviation: '',
			currency: '',
			id: '',
			sign: ''
		},
        errors: {
            currency: {
                status: false,
                text: ''
            },
            abbreviation: {
                status: false,
                text: ''
            }
        },
        nameRules: [
            v => !!v || 'Name is required',
            v => v.length <= 25 || 'Name length must be less than 10 characters'
        ],
        codeRules: [
            v => !!v || 'Code is required',
            v => v.length <= 4 || 'Code can only be 3 characters long'
        ],
        symbolRules: [
            v => v.length <= 8 || 'Symbol length cannot be more than 8 characters'
        ]
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
			this.id = +this.$route.params.id;

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
		},
		// update currency
		updateCurrency() {
			axios.patch('/api/currencies/' + this.id, this.currency)
			.then(res => {
				console.log(res);

				if (res.status == 200) {
					this.$emit('refreshNavCurrencies');
				}
			})
			.catch(error => {
				console.log(error.response);

				this.loadCurrency();
			});
		}
	}
}
</script>