<template>
	<div>
		<v-list two-line v-if="(rates != null && rates.length > 0)">
			<v-subheader>Rates</v-subheader>
			<v-divider></v-divider>

			<v-list-tile v-for="ratio in rates" :key="ratio.id" class="animated slideInRight">
				<v-list-tile-content>
					<v-list-tile-title>{{ ratio.currency }} ({{ ratio.abbreviation }})</v-list-tile-title>
					<v-list-tile-sub-title>{{ ratio.rate }}</v-list-tile-sub-title>
				</v-list-tile-content>
			</v-list-tile>
		</v-list>

		<v-dialog v-model="dialog" width="500" >
			<v-btn slot="activator" class="blue white--text">New Rate!</v-btn>

			<v-card>
				<v-card-title
					class="headline"
					primary-title
					>
					New Rate
				</v-card-title>

				<v-card-text>
					<v-alert :value="true" type="error" v-if="errors.target.status">
			          {{ errors.target.text }}
			        </v-alert>

			        <v-alert :value="true" type="error" v-if="errors.rate.status">
			          {{ errors.rate.text }}
			        </v-alert>

					<v-form ref="form" v-model="valid" lazy-validation>

						<v-layout wrap>
							<v-flex xs12>
								<v-select
								v-model="form.target" 
								:items="currencies"
								item-value="id" 
								item-text="currency" 
								:rules="targetRules"
								label="Currency"
								></v-select>
							</v-flex>

							<v-flex xs12 md4>
								<v-text-field
								v-model="form.rate"
								:rules="rateRules"
								label="Rate"
								required
								></v-text-field>
							</v-flex>

							<v-flex xs12>
								<v-btn
								class="blue white--text"
								:disabled="!valid"
								@click="submit">Store</v-btn>
							</v-flex>
						</v-layout>
					</v-form>
				</v-card-text>
			</v-card>
		</v-dialog>
	</div>
</template>

<script>
export default {
	props: {
		id: {
			required: true,
			type: Number
		}
	},

	data: () => ({
		valid: false,
		form: {
			target: null,
			rate: null
		},
		errors: {
			target: {
				status: false,
				text: ''
			},
			rate: {
				status: false,
				text: ''
			}
		},
		targetRules: [
			v => !!v || 'Currency value is required',
		],
		rateRules: [
			v => !!v || 'Rate value is required',
		],
		currencies: null,
		rates: null,
		dialog: false
	}),

	watch: {
		id: {
			handler(id) {
				this.id = id;
				this.update();
			}
		}
	},

	created() {
		this.update();
	},
	
	methods: {
		// update component information
		update() {
			this.loadRatios();
			this.loadCurrencies();
		},
		// load currencies
		loadCurrencies() {
			axios.get('/api/currencies?hide=' + this.id)
			.then(res => {
				this.currencies = res.data.data;
			})
			.catch(error => console.warn(error));
		},
		// Load Currency Ratios
		loadRatios() {
			axios.get('/api/currencies/' + this.id + '/ratios')
			.then(res => {
				if (res.status == 200) {
					this.rates      = res.data.data;
					this.form.rate  = '';
				}
				else {
					this.rates = null;
					console.log(data);
				}
			})
			.catch(error => console.warn(error));
		},
		// Create New Rate entry
		submit() {
            if (this.$refs.form.validate()) {
				axios.post('/api/currencies/' + this.id + '/ratios', this.form)
				.then(res => {
					if (res.status == 200) {
						this.rates  = res.data.data;
						this.dialog = false;

						this.errors.target.status = false;
						this.errors.rate.status   = false;
					}
					else if (res.status == 422) {
						// errors!
					}
				})
				.catch(error => {
					let errors = error.response.data.errors;
					console.warn(errors);

					// generate alerts for each error
                    if (errors.target) {
                        this.errors.target.status = true;
                        this.errors.target.text   = errors.target[0];
                    } else {
                        this.errors.target.status = false;
                    }
                    if (errors.rate) {
                        this.errors.rate.status = true;
                        this.errors.rate.text   = errors.rate[0];
                    } else {
                        this.errors.rate.status = false;
                    }
				});
            }
        }
	}
}
</script>