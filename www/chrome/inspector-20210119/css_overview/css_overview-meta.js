import{ls as e}from"../platform/platform.js";import{Runtime as i}from"../root/root.js";import{ViewManager as o}from"../ui/ui.js";let r;o.registerViewExtension({location:"panel",id:"cssoverview",commandPrompt:"Show CSS Overview",title:()=>e`CSS Overview`,order:95,loadView:async()=>(await async function(){return r||(await i.Runtime.instance().loadModulePromise("css_overview"),r=await import("./css_overview.js")),r}()).CSSOverviewPanel.CSSOverviewPanel.instance(),experiment:i.ExperimentName.CSS_OVERVIEW});