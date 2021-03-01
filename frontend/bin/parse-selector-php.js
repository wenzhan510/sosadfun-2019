const path = require('path');
const fs = require('fs');
const readline = require('readline');

const ROOT = path.resolve(__dirname, '../../');
const inputFile = path.join(ROOT, 'backend/config/selectors.php');
const outputFile = path.join(ROOT, 'frontend/src/config/request-filter.ts');

async function run () {
  const fileStream = fs.createReadStream(inputFile);
  const rl = readline.createInterface({
    input: fileStream,
    crlfDelay: Infinity
  });

  const result = {};
  let start = false;
  let onNamespace = '';
  let onEnum = '';
  for await (const line of rl) {
    if (!start && /return \[/g.test(line)) {
      start = true;
      continue;
    }
    if (!start) { continue; }
    if (!/\w|\[|\]/g.test(line)) { continue; }

    if (onEnum) {
      if (line.includes(']')) {
        onEnum = '';
        continue;
      }
      const words = line.match(/[\w\u4e00-\u9fa5]+/g);
      if (!words.length) { continue; }
      result[onNamespace][onEnum].push({
        name: words[0],
        comment: words[1],
      }); 
    } else if (onNamespace) {
      if (line.includes(']')) {
        onNamespace = '';
        continue;
      }
      if (!line.includes('[')) { continue; }
      const words = line.match(/[\w]+/g);
      if (!words.length) { continue; }
      onEnum = words[0];
      result[onNamespace][onEnum] = [];
    } else {
      if (!line.includes('[')) { continue; }
      const words = line.match(/[\w]+/g);
      if (!words.length) { continue; }
      onNamespace = words[0].split('_')[0];
      result[onNamespace] = {};
    }
  }

  let requestFilter = '// generated from bin/parse-selector-php.js\n';
  requestFilter += `export namespace RequestFilter {\n`;
  let requestFilterText = `\n\nexport namespace RequestFilterText {\n`;
  for (const namespace in result) {
    requestFilter += `  export namespace ${namespace} {\n`;
    requestFilterText += `  export namespace ${namespace} {\n`;
    for (const enumName in result[namespace]) {
      requestFilter += `    export enum ${enumName} {\n`;
      requestFilterText += `    export const ${enumName}:{[name in RequestFilter.${namespace}.${enumName}]:string} = {\n`;
      for (const enums of result[namespace][enumName]) {
        const correctedName = changeName(enums.name, enums.comment);
        requestFilter += `      ${correctedName} = '${enums.name}', // ${enums.comment}\n`;
        requestFilterText += `      [RequestFilter.${namespace}.${enumName}.${correctedName}]: '${enums.comment}',\n`
      }
      requestFilter += `    }\n`;
      requestFilterText += `    };\n`;
    }
    requestFilter += `  }\n\n`;
    requestFilterText += `  }\n\n`;
  }
  requestFilter += '}';
  requestFilterText += '}';

  fs.writeFileSync(outputFile, requestFilter + requestFilterText, 'utf8');
}

function changeName (name, commentText) {
  // when the name is a number, replace to string
  switch (commentText) {
    case '最新收藏':
      return 'collect';
    case '最新回复':
      return 'reply';
    case '最新章节':
      return 'chapter';
    case '最新创立':
      return 'created';
    case '原创小说':
      return 'yuanchuang';
    case '同人小说':
      return 'tongren';
    default:
      return name;
  }
}

run().catch(console.error);