<template>
	<v-container grid-list-sm>

		<v-alert :value="true" type="error" v-if="errors.base.status">{{ errors.base.text }}</v-alert>
		<v-alert :value="true" type="error" v-if="errors.target.status">{{ errors.target.text }}</v-alert>
		<v-alert :value="true" type="error" v-if="errors.value.status">{{ errors.value.text }}</v-alert>

		<v-layout row wrap>
			<!-- Left Column -->
			<v-flex d-flex xs12 md8>
				<v-layout column>
					<!-- Base Currency Box -->
					<v-flex  d-flex>
						<h1>Convert Currency</h1>
					</v-flex>
					<v-flex d-flex>
						<v-card color="blue-grey darken-4" dark tile flat>
							<v-card-text>
								<v-layout wrap>
									<v-flex xs12 md6>
										<v-select
										v-model="convert.base" 
										:items="items"
										item-value="id" 
										item-text="currency" 
										item-avatar="img"
										label="Currency"
										outline
										></v-select>
									</v-flex>
									<v-flex xs12 md6>
										<v-text-field
						                v-model="convert.value"
						                label="Value"
						                class="padding-bottom"
						                outline
						                ></v-text-field>
									</v-flex>
								</v-layout>
							</v-card-text>
						</v-card>
					</v-flex>
					<!-- Target Currency Box -->
					<v-flex d-flex>
						<h1>To</h1>
					</v-flex>
					<v-flex d-flex>
					  	<v-card color="blue-grey darken-4" dark tile flat>
							<v-card-text>
								<v-select
								v-model="convert.target" 
								:items="items"
								item-value="id" 
								item-text="currency" 
								item-avatar="img"
								label="Currency"
								outline
								></v-select>

								<v-btn
				                color="orange"
				                block
				                large
				                @click="convertCurrenty">Convert</v-btn>
							</v-card-text>
						</v-card>
					</v-flex>
				</v-layout>
			</v-flex>
			<!-- Left Column End -->

			<!-- Right Column Start -->
			<v-flex d-flex xs12 md4>
				<v-alert :value="true" type="info" v-if="notFound">Rate not found.</v-alert>

				<v-card color="blue-grey darken-4" v-if="showResults && !!results" dark tile flat class="results-card">
					<v-card-text>
						<p class="big">{{ results.base.value }} {{ results.base.sign }} =</p>
						<h2>{{ results.target.value }} {{ results.target.sign }}</h2>
						<p class="grey--text">Last Updated {{ results.lastUpdated }}</p>
					</v-card-text>
				</v-card>
			</v-flex>
			<!-- Right Column End -->
		</v-layout>
	</v-container>
</template>

<style>
.padding-bottom .v-text-field__slot { 
	padding: 1px; 
}
.results-card p { 
	margin-bottom: 5px;
    font-size: 1.2rem;
    text-align: center;
}
.results-card p.big {
	font-size: 1.8rem;
}
.results-card h2 {
	font-size: 3rem;
	text-align: center;
}
</style>

<script>
export default {
	data: () => ({
		items: null,
		showResults: false,
		notFound: false,
		results: null,
		convert: {
			base: null,
			target: null,
			value: null
		},
		errors: {
			base: {
				status: false,
				text: ''
			},
			target: {
				status: false,
				text: ''
			},
			value: {
				status: false,
				text: ''
			}
		}
	}),

	mounted() {
      this.loadCurrencies();
    },

	methods: {
		// load all available currencies and store them
		loadCurrencies() {
			axios.get('/api/currencies')
			.then(res => {
				this.$parent.noConnection = false;
				this.items = res.data.data;
			})
			.catch(error => {
				console.warn(error.response);
				this.$parent.noConnection = true;
			});
		},
		// convert currency
		convertCurrenty() {
			let url = '/api/convert?base=' + this.convert.base + '&target=' + this.convert.target + '&value=' + this.convert.value;
			axios.get(url)
			.then(res => {
				// successful action
				if (res.status == 200) {
					this.showResults = true;
					this.results     = res.data.data;
				}

				this.hideErrors();
			})
			.catch(error => {
				this.showResults = false;

				if (error.response.status == 404) {
					this.hideErrors();

					this.notFound = true;
				}
				else if (error.response.status == 422) {
					let resp = error.response.data.errors;

					// generate alerts for each error
	                if (resp.base) {
	                    this.errors.base.status = true;
	                    this.errors.base.text   = resp.base[0];
	                } else {
	                    this.errors.base.status = false;
	                }
	                if (resp.target) {
	                    this.errors.target.status = true;
	                    this.errors.target.text   = resp.target[0];
	                } else {
	                    this.errors.target.status = false;
	                }
	                if (resp.value) {
	                    this.errors.value.status = true;
	                    this.errors.value.text   = resp.value[0];
	                } else {
	                    this.errors.value.status = false;
	                }
	            }
			});
		},
		// 
		hideErrors() {
			this.notFound = false;
			this.errors.base.status    = false;
			this.errors.target.status  = false;
			this.errors.value.status   = false;
		}
	}
}
</script>