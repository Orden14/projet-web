import { startStimulusApp } from "@symfony/stimulus-bridge";

import $ from "jquery";
global.$ = $;
global.jQuery = $;

export const app = startStimulusApp(require.context("@symfony/stimulus-bridge/lazy-controller-loader!./controllers", true, /\.[jt]sx?$/));
