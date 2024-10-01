const fs = require('node:fs');
const path = require('node:path');
const archiver = require('archiver');


const ignore = require('./ignored');
const pack = require('../package.json');
const name = 'stancer';

const magentoPackageName = 'stancer/module-payments';
const packagistPackageName = 'stancer/cms-magento';

const composer = path.join(__dirname,'../composer.json');

const changeModuleName = (oldName, newName) => {
    const file = fs.readFileSync(composer, {encoding: 'utf-8'});

    const data = file.replace(oldName, newName);

    fs.writeFileSync(composer, data);
};

changeModuleName(packagistPackageName, magentoPackageName);

const output = fs.createWriteStream(
    path.join(__dirname, '../', `${name}-${pack.version}.zip`)
);
const archive = archiver(
    'zip',
    {
        zlib: {
            level: 9
        }
    }
);

output.on('close', () => {
    console.log(`Archive size: ${archive.pointer()} bytes`);
});

archive.on('warning', (err) => {
    throw err;
});

archive.on('error', (err) => {
    throw err;
});

archive.pipe(output);

archive.glob('**', { ignore }, { prefix: `${name}/` });
archive.finalize().then(() => changeModuleName(magentoPackageName, packagistPackageName));

