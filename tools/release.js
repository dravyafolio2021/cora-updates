const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Determine industry and arguments
const args = process.argv.slice(2);
const industryArg = args.find(arg => arg.startsWith('--industry='));
const versionArg = args.find(arg => arg.startsWith('--version='));
const changelogArg = args.find(arg => arg.startsWith('--changelog='));

const industry = industryArg ? industryArg.split('=')[1] : 'studio';
const customVersion = versionArg ? versionArg.split('=')[1] : null;
const changelog = changelogArg ? changelogArg.split('=')[1] : 'Updates and optimizations';

console.log(`🚀 Starting Release Pipeline for Industry: [${industry}]`);

// Paths
const baseDir = path.resolve(__dirname, '..');
const pluginsDir = path.join(baseDir, 'app', 'public', 'wp-content', 'plugins');

let pluginDirName = '';
let pluginFileName = '';
let pluginSlug = '';

if (industry === 'studio') {
    pluginDirName = 'cora-studio-ai-locked';
    pluginFileName = 'cora-studio-ai.php';
    pluginSlug = 'cora-studio-ai-locked';
} else if (industry === 'real-estate') {
    pluginDirName = 'cora-real-estate';
    pluginFileName = 'cora-real-estate.php';
    pluginSlug = 'cora-real-estate';
} else {
    console.error(`❌ Invalid industry: ${industry}`);
    process.exit(1);
}

const pluginPath = path.join(pluginsDir, pluginDirName);
const mainFilePath = path.join(pluginPath, pluginFileName);

if (!fs.existsSync(mainFilePath)) {
    console.error(`❌ Plugin main file not found at: ${mainFilePath}`);
    process.exit(1);
}

// 1. Version Reading & Bumping
let mainFileContent = fs.readFileSync(mainFilePath, 'utf8');
const versionRegex = /\*\s*Version:\s*([0-9.]+)/i;
const match = mainFileContent.match(versionRegex);

if (!match) {
    console.error('❌ Could not find Version header in plugin main file.');
    process.exit(1);
}

const currentVersion = match[1];
let nextVersion = customVersion;

if (!nextVersion) {
    // Auto increment patch version
    const parts = currentVersion.split('.').map(Number);
    parts[parts.length - 1]++;
    nextVersion = parts.join('.');
}

console.log(`   Current Version: ${currentVersion}`);
console.log(`   Next Version:    ${nextVersion}`);

// Update main file version
mainFileContent = mainFileContent.replace(versionRegex, ` * Version: ${nextVersion}`);
fs.writeFileSync(mainFilePath, mainFileContent, 'utf8');
console.log(`✅ Updated Version in ${pluginFileName} to ${nextVersion}`);

// 2. Build ZIP Package
console.log(`📦 Packaging plugin folder: ${pluginDirName}...`);
const zipFileName = `${pluginSlug}.zip`;
const updatesDir = path.join(baseDir, 'updates');

if (!fs.existsSync(updatesDir)) {
    fs.mkdirSync(updatesDir);
}

// Run ZIP from the plugins directory context to package correctly
try {
    execSync(`zip -r "${path.join(updatesDir, zipFileName)}" "${pluginDirName}"`, {
        cwd: pluginsDir,
        stdio: 'inherit'
    });
    console.log(`✅ ZIP package generated successfully at: updates/${zipFileName}`);
} catch (err) {
    console.error('❌ ZIP packaging failed:', err.message);
    process.exit(1);
}

// 3. Update Updates JSON Metadata
const updatesJsonPath = path.join(updatesDir, `${pluginSlug}.json`);
const rawGithubZipUrl = `https://raw.githubusercontent.com/dravyafolio2021/heycora/main/updates/${zipFileName}`;

const updatesData = {
    name: industry === 'studio' ? 'Cora for Studio' : 'Cora Real Estate',
    slug: pluginSlug,
    version: nextVersion,
    download_url: rawGithubZipUrl,
    sections: {
        description: industry === 'studio' 
            ? 'The ultimate AI workspace and CRM for modern photography studios.' 
            : 'The ultimate white-label minimalist CRM for real estate agencies.',
        changelog: `<h4>${nextVersion}</h4><ul><li>${changelog}</li></ul>`
    }
};

fs.writeFileSync(updatesJsonPath, JSON.stringify(updatesData, null, 4), 'utf8');
console.log(`✅ Updates manifest generated at: updates/${pluginSlug}.json`);

// 4. Instructions
console.log('\n🌟 RELEASE BUILD COMPLETE!');
console.log('To publish this release to GitHub and dispatch updates to all client sites, run:');
console.log(`   git add updates/`);
console.log(`   git commit -m "Release v${nextVersion}"`);
console.log(`   git push origin main`);
console.log('\nClient sites will automatically detect the new version and display the WordPress Update banner.');
