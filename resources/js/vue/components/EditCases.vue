<template>
  <v-dialog v-model="dialog" width="500">
    <v-card>
      <v-form v-model="formIsValid" ref="form">
        <v-container>
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="privateCases.new_confirmed"
                :rules="casesRules"
                label="New confirmed"
                outlined
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="privateCases.total_confirmed"
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
                v-model="privateCases.new_deaths"
                :rules="casesRules"
                label="New deaths"
                outlined
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="privateCases.total_deaths"
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
                v-model="privateCases.new_recovered"
                :rules="casesRules"
                label="New recovered"
                outlined
                required
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="privateCases.total_recovered"
                :rules="casesRules"
                label="Total recovered"
                outlined
                required
              ></v-text-field>
            </v-col>
          </v-row>
        </v-container>
      </v-form>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          class="color-white"
          depressed
          color="#14213d"
          :disabled="!formIsValid || submitting"
          @click="submit"
        >submit</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script>
import axios from "axios";

export default {
  name: "EditCases",
  props: ["show", "cases"],
  data() {
    return {
      formIsValid: false,
      casesRules: [
        v => !!v || "This field is required",
        v => !isNaN(v) || "This field must be number",
        v => Number.isInteger(Number(v)) || "This field must be integer",
        v => v >= 0 || "This field must be positive"
      ],
      submitting: false,
      dialog: false,
      privateCases: this.cases
    };
  },
  methods: {
    async submit() {
      this.submitting = true;
      try {
        const res = await axios.put("http://127.0.0.1:8000/api/cases/", {
          country_code: this.privateCases.country_code,
          new_confirmed: this.privateCases.new_confirmed,
          total_confirmed: this.privateCases.total_confirmed,
          new_deaths: this.privateCases.new_deaths,
          total_deaths: this.privateCases.total_deaths,
          new_recovered: this.privateCases.new_recovered,
          total_recovered: this.privateCases.total_recovered
        });

        if (res.status !== 200) {
          throw new Error(res.data.message || "Somthing went wrong. Try again");
        }

        this.$root.$emit("add_cases", res.data);

        this.dialog = false;

        this.$root.$emit("notify", {
          message: "Cases have been updated",
          type: "#8ACA2B"
        });
      } catch (e) {
        this.$root.$emit("notify", { message: e.message, type: "red" });
      }

      this.submitting = false;
    }
  },
  watch: {
    dialog() {
      this.$emit("update:state", this.dialog);
    },
    show() {
      this.dialog = this.show;
    },
    cases() {
      this.privateCases = this.cases;
    }
  }
};
</script>

