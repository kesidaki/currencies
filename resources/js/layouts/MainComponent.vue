<template>
  <v-app
    id="inspire"
    light
  >
    <v-navigation-drawer
      v-model="drawer"
      fixed
      clipped
      app
    >
      <v-list dense>

        <v-list-tile to="/">
            <v-list-tile-action>
              <v-icon>home</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>
                Convert Currency
              </v-list-tile-title>
            </v-list-tile-content>
        </v-list-tile>

        <v-subheader class="mt-3 grey--text text--darken-1">Manage Currencies</v-subheader>
        <v-list>
          <v-list-tile to='/new-currency'>
            <v-list-tile-action>
              <v-icon>add</v-icon>
            </v-list-tile-action>
            <v-list-tile-title>New Currency</v-list-tile-title>
          </v-list-tile>

          <v-list-tile v-for="item in currencies" :key="item.text" :to="{ name: 'Currency', params: {id: item.id} }">
            <v-list-tile-title>({{ item.abbreviation }}) {{ item.currency }}</v-list-tile-title>
          </v-list-tile>
        </v-list>
      </v-list>

    </v-navigation-drawer>
    <v-toolbar
      dense
      fixed
      clipped-left
      app
    >
      <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
      <v-toolbar-title class="mr-5 align-center">
        <span class="title">Currency Converter</span>
      </v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
    <v-content>
      <v-container>

        <v-alert :value="true" type="error" v-if="noConnection">
          Could not connect to the server. Please check your internet connection and try again. If the problem persists, contact the Administrator.
        </v-alert>

        <router-view @refreshNavCurrencies="loadCurrencies"></router-view>
        
      </v-container>
    </v-content>
  </v-app>
</template>

<script>
  export default {
    data: () => ({
      drawer: true,
      noConnection: false,
      items: [],
      currencies: []
    }),
    mounted() {
      this.loadCurrencies();
    },
    methods: {
      // load all available currencies and store them
      loadCurrencies() {
        axios.get('/api/currencies')
        .then(res => {
          this.noConnection = false;
          this.currencies = res.data.data;
        })
        .catch(error => {
          console.warn(error.response);
          this.noConnection = true;
        });
      }
    }
  }
</script>