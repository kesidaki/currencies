<template>
    <v-form ref="form" v-model="valid" lazy-validation>
        <h1>New Currency Page</h1>

        <v-alert :value="true" type="success" v-if="stored">Currency Added</v-alert>

        <v-alert :value="true" type="error" v-if="errors.abbreviation.status">{{ errors.abbreviation.text }}</v-alert>
        <v-alert :value="true" type="error" v-if="errors.currency.status">{{ errors.currency.text }}</v-alert>

        <v-layout wrap>
            <v-flex xs12>
                <v-text-field
                v-model="form.currency"
                :rules="nameRules"
                :counter="25"
                label="Currency"
                required
                ></v-text-field>
            </v-flex>

            <v-flex xs12 md4>
                <v-text-field
                v-model="form.abbreviation"
                :rules="codeRules"
                :counter="4"
                label="Abbreviation"
                required
                ></v-text-field>
            </v-flex>

            <v-flex xs12 md8>
                <v-text-field
                v-model="form.sign"
                :rules="symbolRules"
                :counter="8"
                label="Sign / Symbol"
                ></v-text-field>
            </v-flex>

            <v-flex xs12>
                <v-btn
                color="blue white--text"
                :disabled="!valid"
                @click="submit">Store</v-btn>
            </v-flex>
        </v-layout>
    </v-form>
</template>

<script>
export default {
    data: () => ({
        valid: false,
        stored: false,
        form: {
            currency: '',
            abbreviation: '',
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

    methods: {
        submit() {
            if (this.$refs.form.validate()) {
                axios.post('/api/currencies', this.form)
                    .then(res => {
                        // successfull response
                        if (res.status == 200) {
                            this.stored = true;
                            this.clear();
                            this.$emit('refreshNavCurrencies');

                            // disable status messages
                            this.errors.abbreviation.status = false;
                            this.errors.currency.status = false;
                        }
                    })
                    // error response
                    .catch(error => {
                        let errors = error.response.data.errors;

                        // generate alerts for each error
                        if (errors.abbreviation) {
                            this.errors.abbreviation.status = true;
                            this.errors.abbreviation.text   = errors.abbreviation[0];
                        } else {
                            this.errors.abbreviation.status = false;
                        }
                        if (errors.currency) {
                            this.errors.currency.status = true;
                            this.errors.currency.text   = errors.currency[0];
                        } else {
                            this.errors.currency.status = false;
                        }
                    });
            }
        },
        clear() {
            // this.$refs.form.reset();
            this.form.currency = '';
            this.form.abbreviation = '';
            this.form.sign = '';
        }
    }
  }
</script>