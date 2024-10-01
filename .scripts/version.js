const fs = require('node:fs');
const globSync = require('glob/sync');
const path = require('node:path');
const pack = require('../package.json');

const globOptions = {
    ignore: [
        'node_modules/**',
        'scripts/**',
        'vendor/**'
    ]
};
const currentYear = String(new Date().getFullYear());
const currentDate = new Date().toISOString().split('T').at(0);

for (file of globSync('**/*.php', globOptions))
{
    const filepath = path.join(process.cwd(), file);

    const content = fs.readFileSync(filepath, { encoding: 'utf-8' });

    const data = content
        .replaceAll(/\* @since unreleased/g, `* @since ${pack.version}`)
        .replace(/\* Version:.+/, `* Version:     ${pack.version}`)
        .replace(
            /\* @copyright (\d{4})(?:-\d{4})?\s+Stancer.+/,
            (_match, date) => {
                if (date === currentYear) {
                    return `* @copyright ${date} Stancer / Iliad 78`;
                }

                return `* @copyright ${date}-${currentYear} Stancer / Iliad 78`;
            }
        );

    fs.writeFileSync(file, data);
}

const changelog = fs.readFileSync('CHANGELOG.md', { encoding: 'utf-8' });

fs.writeFileSync(
    'CHANGELOG.md',
    changelog.replace(
        /##\s*\[?[uU]nreleased?\]?/,
        `## [${pack.version}] - ${currentDate}`
    )
);

const config = path.join('Model', 'Config.php');
const configfile = fs.readFileSync(config, { encoding: 'utf8' });

fs.writeFileSync(
    config,
    configfile.replace(
        /MODULE_VERSION = .+/,
        `MODULE_VERSION = '${pack.version}';`
    )
);

const composer = fs.readFileSync('composer.json', { encoding: 'utf8' });

fs.writeFileSync(
    'composer.json',
    composer.replace(/"version":.+/, `"version": "${pack.version}",`)
);

const readme = fs.readFileSync('README.md', { encoding: 'utf8' });

fs.writeFileSync(
    'README.md',
    readme
        .replace(/Stable tag:.+/, `Stable tag: ${pack.version}`)
        .replace(
            /=\s+(Version\s+)?[uU]nreleased?\s+=/,
            `= Version ${pack.version} =`
        )
);

const license = fs.readFileSync('LICENSE', { encoding: 'utf8' });

fs.writeFileSync(
    'LICENSE',
    license.replace(
        /^Copyright \(c\) (\d{4})(?:-\d{4})?\s+Stancer.+/,
        (_match, date) => {
            if (date === currentYear) {
                return `Copyright (c) ${date} Stancer / Iliad 78`;
            }

            return `Copyright (c) ${date}-${currentYear} Stancer / Iliad 78`;
        }
    )
);
