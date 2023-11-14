"use strict";

const pkg = require("../../package.json");
const year = new Date().getFullYear();

function getBanner(pluginFilename) {
  return `/*!
  * ${pluginFilename ? ` ${pluginFilename}` : ""} v${
    pkg.version
  } ({REPLACE_ME_URL})
  * Copyright 2013-${year} {REPLACE_ME_AUTHOR}
  * Licensed under GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)
  */`;
}

module.exports = getBanner;
