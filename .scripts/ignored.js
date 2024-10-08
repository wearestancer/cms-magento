module.exports = [];

// These scripts
module.exports.push('.scripts/**');
module.exports.push('**.zip');

// Gulp process
module.exports.push('node_modules/**');
module.exports.push('package*');
module.exports.push('pnpm*');
module.exports.push('tsconfig.*');

// Composer stuff
module.exports.push('reports/**');
module.exports.push('vendor/**');

//git stuff
module.exports.push('.git*');
module.exports.push('.editorconfig');

//Dev stuff
module.exports.push('.devcontainer');

//Magento-coding-standard
module.exports.push('magento-coding*/**');

