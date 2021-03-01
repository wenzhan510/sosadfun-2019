import XLSX from 'xlsx';
// test bbcode samples in provided excel
const excelFileAddress = 'http://34.70.54.149/files/testData/bbcodedata.xlsx';
const testSheetNumber = 3; // can be 0 - 3

export function formatTestData(data:string) {
  if (typeof(data) != 'string') {
    data = '' + data;
  }
  return data.replace(/↵/g, '\n');
}

export function loadTestData () {
  return fetch(excelFileAddress)
    .then((response) => {
      if (!response.ok) {
        throw new Error('HTTP error, status = ' + response.status);
      }
      return response.arrayBuffer();
    })
    .then((buffer) => {
      /* convert data to binary string */
      const data = new Uint8Array(buffer);
      const arr = new Array();
      for (let i = 0; i != data.length; ++i) {
        arr[i] = String.fromCharCode(data[i]);
      }
      const bstr = arr.join('');

      /* Call XLSX */
      const workbook = XLSX.read(bstr, {type:'binary'});
      /* DO SOMETHING WITH workbook HERE */
      const first_sheet_name = workbook.SheetNames[testSheetNumber];
      const worksheet:any = workbook.Sheets[first_sheet_name];

      const columnA = new Array();

      for (const z in worksheet) {
        if (z.toString()[0] === 'A') {
          const str:string = worksheet[z].v;
          columnA.push(str);
        }
      }
      return columnA;
      });
}
