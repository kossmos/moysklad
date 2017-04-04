const
	fs = require('fs'),
	log = require('cllc')(),
	excelize = require('excelize'),
	data1 = require('./Product1.json').rows,
	data2 = require('./Product2.json').rows,
	data3 = require('./Product3.json').rows,
	data4 = require('./Product4.json').rows,
	data = [data1, data2, data3, data4],
	array = [];


log('Начало работы');
log.start('Обработано %s товаров из 347');

for (var ix = 0; ix < data.length; ix++) {
	// console.log(data[ix].length);

	for (var ixx = 0; ixx < data[ix].length; ixx++) {
		log.step();

		// array[data[ix][ixx].code] = data[ix][ixx].meta.href;
		// console.log(data[ix][ixx].code);
		array.push({
			sku: data[ix][ixx].code,
			id: data[ix][ixx].meta.href.replace('https://online.moysklad.ru/api/remap/1.1/entity/product/', '')
		});
	}

	fs.writeFileSync('./Product.json', JSON.stringify(array, null, 4));
}

excelize(array, './', 'Product.xlsx', 'sheet', function(err) {
	if (err) throw err;

	log(array.length + ' товар(ов) сохранено в .xlsx');
});

log.finish();
log('Работа закончена');