<template>
  <span>
    <v-btn
      depressed
      class="top-left"
      color="error"
      @click.stop="drawer = !drawer"
      :disabled="sharedData.countriesWithNoData === null"
    >Report coronavirus cases</v-btn>

    <v-navigation-drawer v-model="drawer" absolute persistent width="100%" style="z-index: 2">
      <v-row class="full-height">
        <v-col clas="col-12 col-lg-6" style="position: relative">
          <div class="map-cover"></div>
          <div class="full-size" ref="addCasesMap"></div>
        </v-col>
        <v-col class="form col-12 col-lg-6" style="height: 50%;" align-self="center">
          <v-container fluid>
            <div class="text-h4 mb-6">Report coronavirus cases</div>
            <v-form v-if="sharedData.countriesWithNoData !== null" v-model="formIsValid" ref="form">
              <v-container>
                <v-row>
                  <v-col cols="12" md="4">
                    <v-select
                      @click="goHome"
                      @input="zoomCheck"
                      @blur="zoomCheck"
                      v-model="selectedCountry"
                      :items="sharedData.countriesWithNoData"
                      item-text="name"
                      item-value="id"
                      label="Select a country"
                      :rules="countryRules"
                      outlined
                      required
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="new_confirmed"
                          :rules="casesRules"
                          label="New confirmed"
                          outlined
                          required
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="total_confirmed"
                          :rules="casesRules"
                          label="Total confirmed"
                          outlined
                          required
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="new_deaths"
                          :rules="casesRules"
                          label="New deaths"
                          outlined
                          required
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="total_deaths"
                          :rules="casesRules"
                          label="Total deaths"
                          outlined
                          required
                        ></v-text-field>
                      </v-col>
                    </v-row>
                    <v-row>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="new_recovered"
                          :rules="casesRules"
                          label="New recovered"
                          outlined
                          required
                        ></v-text-field>
                      </v-col>
                      <v-col cols="12" md="6">
                        <v-text-field
                          v-model="total_recovered"
                          :rules="casesRules"
                          label="Total recovered"
                          outlined
                          required
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
                <v-btn
                  class="mr-4 color-white"
                  depressed
                  color="#14213d"
                  :disabled="!formIsValid || submitting"
                  @click="submit"
                >submit</v-btn>
              </v-container>
            </v-form>
          </v-container>
        </v-col>
      </v-row>

      <v-btn icon depressed class="top-left" @click.stop="drawer = !drawer">
        <v-icon>{{ mdiClose }}</v-icon>
      </v-btn>
    </v-navigation-drawer>
  </span>
</template>

<script>
import { mdiClose } from "@mdi/js";
import { sharedData, sharedMethods } from "./shared";
import axios from "axios";

export default {
  name: "AddCases",
  data() {
    return {
      sharedData,
      drawer: false,
      selectedCountry: null,
      polygonSeries: null,
      formIsValid: false,
      submitting: false,
      countryRules: [v => !!v || "This field is required"],
      casesRules: [
        v => !!v || "This field is required",
        v => !isNaN(v) || "This field must be number",
        v => Number.isInteger(Number(v)) || "This field must be integer",
        v => v >= 0 || "This field must be positive"
      ],
      new_confirmed: null,
      total_confirmed: null,
      new_deaths: null,
      total_deaths: null,
      new_recovered: null,
      total_recovered: null,
      mdiClose: mdiClose
    };
  },
  watch: {
    selectedCountry(newCountry, oldCountry) {
      if (oldCountry) {
        this.polygonSeries.getPolygonById(oldCountry).isHover = false;
      }
      if(!newCountry) return
      const target = this.polygonSeries.getPolygonById(newCountry);
      target.isHover = true;

      this.polygonSeries.chart.zoomToMapObject(target);
    }
  },
  methods: {
    async submit() {
      this.submitting = true;
      try {
        const res = await axios.post("http://127.0.0.1:8000/api/cases/", {
          country_code: this.selectedCountry,
          new_confirmed: this.new_confirmed,
          total_confirmed: this.total_confirmed,
          new_deaths: this.new_deaths,
          total_deaths: this.total_deaths,
          new_recovered: this.new_recovered,
          total_recovered: this.total_recovered
        });

        if (res.status !== 201) {
          throw new Error(res.data.message || "Somthing went wrong. Try again");
        }

        this.$root.$emit("add_cases", res.data);

        this.$refs.form.reset();
        this.$refs.form.validate();
        this.goHome();

        this.drawer = false;

        this.$root.$emit("notify", {
          message: "Cases have been reported",
          type: "#8ACA2B"
        });
      } catch (e) {
        this.$root.$emit("notify", { message: e.message, type: "red" });
      }

      this.submitting = false;
    },
    goHome() {
      this.polygonSeries.chart.goHome();
    },
    zoomCheck() {
      if(!this.selectedCountry) return
      const target = this.polygonSeries.getPolygonById(this.selectedCountry);
      target.isHover = true;

      this.polygonSeries.chart.zoomToMapObject(target);
    }
  },
  mounted() {
    this.polygonSeries = sharedMethods.makeMap(this.$refs.addCasesMap);
  }
};
</script>