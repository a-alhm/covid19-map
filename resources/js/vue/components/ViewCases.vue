<template>
  <div>
    <v-navigation-drawer
      stateless
      v-model="drawer"
      absolute
      persistent
      width="100%"
      style="z-index: 2"
    >
      <v-btn icon depressed class="top-left" @click.stop="drawer = !drawer">
        <v-icon>{{ mdiClose }}</v-icon>
      </v-btn>

      <v-row class="background-white full-height">
        <v-col clas="col-12 col-lg-6" style="position: relative">
          <div class="map-cover"></div>
          <div class="full-size" ref="viewMap"></div>
        </v-col>
        <v-col
          class="form col-12 col-lg-6"
          style="height: 50%; position: relative"
          align-self="center"
        >
          <v-btn
            @click="showDialog = !showDialog"
            class="top-right color-white"
            color="#14213d"
          >Edit data</v-btn>
          <div v-if="country">
            <div class="text-h4 ma-5 text-center">
              <b>{{ country.name }}</b>
            </div>
            <v-row class="font20">
              <v-col class="col-12 col-lg-6 pl-16">
                <div class="pa-3">New Confirmed: {{ country.new_confirmed || "N/A" }}</div>
                <div class="pa-3">New Deaths: {{ country.new_deaths|| "N/A" }}</div>
                <div class="pa-3">New Recovered: {{ country.new_recovered|| "N/A" }}</div>
              </v-col>
              <v-col class="col-12 col-lg-6 pl-16">
                <div class="pa-3">Total Confirmed: {{ country.total_confirmed || "N/A" }}</div>
                <div class="pa-3">Total Deaths: {{ country.total_deaths || "N/A" }}</div>
                <div class="pa-3">Total Recovered: {{ country.total_recovered || "N/A" }}</div>
              </v-col>
            </v-row>
            <v-row class="font20 text-center mt-4">
              <v-col class="col-12 col-lg-6">
                <div
                  class="pa-3"
                >Death Rate: {{ country.death_rate ? country.death_rate + "%" : "N/A" }}</div>
              </v-col>
              <v-col class="col-12 col-lg-6">
                <div
                  class="pa-3"
                >Recovery Rate: {{ country.recovery_rate ? country.recovery_rate + "%" : "N/A" }}</div>
              </v-col>
            </v-row>
          </div>
        </v-col>
      </v-row>
      <EditCases v-if="country"
        :show="showDialog"
        :cases="{
           country_code: country.id,
           new_confirmed: country.new_confirmed, 
           new_deaths: country.new_deaths, 
           new_recovered: country.new_recovered, 
           total_confirmed: country.total_confirmed, 
           total_deaths: country.total_deaths, 
           total_recovered: country.total_recovered  
           }"
        @update:state="showDialog = $event"
      />
    </v-navigation-drawer>
  </div>
</template>

<script>
import EditCases from "./EditCases";
import { sharedMethods } from "./shared";

import { mdiClose } from "@mdi/js";

export default {
  name: "ViewCases",
  components: {
    EditCases
  },
  data() {
    return {
      drawer: false,
      mdiClose: mdiClose,
      country: null,
      showDialog: false
    };
  },
  watch: {
    drawer(newValue) {
      if (!newValue) {
        this.polygonSeries.chart.goHome();
      }
    }
  },
  mounted() {
    this.polygonSeries = sharedMethods.makeMap(this.$refs.viewMap);

    this.$root.$on("view_cases", payload => {
      if (this.country) {
        this.polygonSeries.getPolygonById(this.country.id).isHover = false;
      }

      const id = payload.country.id;

      const target = this.polygonSeries.getPolygonById(id);
      target.isHover = true;

      this.polygonSeries.chart.zoomToMapObject(target);

      this.drawer = true;
      this.country = payload.country;
    });
  }
};
</script>

<style scoped>
.background-white {
  background-color: white;
}

.font20 {
  font-size: 20px;
}
</style>