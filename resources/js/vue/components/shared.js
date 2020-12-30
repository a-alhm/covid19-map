import * as am4core from "@amcharts/amcharts4/core";
import * as am4maps from "@amcharts/amcharts4/maps";
import am4geodata_worldLow from "@amcharts/amcharts4-geodata/worldLow";
import am4themes_animated from "@amcharts/amcharts4/themes/animated";

am4core.useTheme(am4themes_animated);

export const sharedData = {
    countriesWithNoData: null
}

export const sharedMethods = {
    makeMap(DOM, opts) {

        if (!opts) opts = {}

        const map = am4core.create(DOM, am4maps.MapChart);

        if (opts.maxZoomLevel) {
            map.maxZoomLevel = opts.maxZoomLevel;
        }

        map.seriesContainer.draggable = false;
        map.chartContainer.wheelable = false;

        map.geodata = am4geodata_worldLow;

        map.projection = new am4maps.projections.Miller();

        const polygonSeries = new am4maps.MapPolygonSeries();
        polygonSeries.useGeodata = true;
        map.series.push(polygonSeries);

        const polygonTemplate = polygonSeries.mapPolygons.template;
        polygonTemplate.fill = am4core.color("#E5E5E5");

        const hs = polygonTemplate.states.create("hover");
        hs.properties.fill = am4core.color("#fca311");

        if (opts.tooltip) {
            polygonTemplate.tooltipHTML = opts.tooltip.html
            polygonSeries.tooltip.getFillFromObject = false;
            polygonSeries.tooltip.background.fill = am4core.color(opts.tooltip.color);
        }

        polygonSeries.exclude = [
            "AQ",
            "UM-DQ",
            "UM-FQ",
            "UM-HQ",
            "UM-JQ",
            "UM-MQ",
            "UM-WQ"
        ];

        return polygonSeries
    }
}

