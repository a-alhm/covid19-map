<template>
  <div class="full-size" ref="landingPageMap"></div>
</template>
<script>
import axios from "axios";
import { sharedData, sharedMethods } from "./shared";

const asyncTimeout = amount => new Promise(res => setTimeout(res, amount));

export default {
  name: "Map",
  methods: {
    async getCases() {
      try {
        this.cases = await axios.get("http://127.0.0.1:8000/api/cases/");
      } catch (e) {
        console.error(e);
        await asyncTimeout(3000);
        return this.getCases();
      }
    }
  },
  data() {
    return {
      cases: null
    };
  },
  mounted() {
    const polygonSeries = sharedMethods.makeMap(this.$refs.landingPageMap, {
      maxZoomLevel: 1,
      tooltip: {
        html: "<b style='font-size: 26px;'>{name}</b> {details}",
        color: "#67b7dc"
      }
    });

    const psd = polygonSeries.data;

    polygonSeries.mapPolygons.template.events.on("hit", ev => {
      const country = psd.find(
        ele => ele.id === ev.target.dataItem.dataContext.id
      );

      this.$root.$emit("view_cases", { country: country });
    });

    const addCases = country => {
      const index = psd.findIndex(ele => ele.id === country.country_code);

      if (psd[index]) {
        psd[index].new_confirmed = country.new_confirmed.toString();
        psd[index].total_confirmed = country.total_confirmed.toString();
        psd[index].new_deaths = country.new_deaths.toString();
        psd[index].total_deaths = country.total_deaths.toString();
        psd[index].new_recovered = country.new_recovered.toString();
        psd[index].total_recovered = country.total_recovered.toString();

        psd[index].recovery_rate = Math.round(
          (country.total_recovered / country.total_confirmed) * 100
        ).toString();
        psd[index].death_rate = Math.round(
          (country.total_deaths / country.total_confirmed) * 100
        ).toString();

        psd[index].details = `
          <div style="margin-top: 10px;">
            <div>
              New Confirmed: 
              <span style="color:#FFEEAA; font-weight: bold;">
                ${country.new_confirmed}
              </span>
            </div>
            <div>
              New Deaths: 
              <span style="color:red; font-weight: bold;">
                ${country.new_deaths}
              </span>
            </div>
            <div>
              New Recovered: 
              <span style="color:#8ACA2B; font-weight: bold;">
                ${country.new_recovered}
              </span>
            </div>            
          </div>

          `;
      }
    };
    this.getCases().then(() => {
      this.cases.data.forEach(addCases);
      sharedData.countriesWithNoData = psd.filter(
        ele => ele.new_confirmed === undefined
      );

      this.$root.$on("add_cases", data => {
        addCases(data);
        sharedData.countriesWithNoData = psd.filter(
          ele => ele.new_confirmed === undefined
        );
      });
    });
  }
};
</script>

