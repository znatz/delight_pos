
SELECT data_stock.chrStockCode AS chrID,
goods.chrCode AS chrCode,
goods.chrName AS chrName,
`group`.chrName AS chrGroup,
seller.chrName AS chrSeller,
maker.chrName AS chrMaker,
goods.chrColor AS chrColor,
goods.chrSize AS chrSize,
goods.intCost AS intCost,
goods.intPrice AS intPrice,
data_stock.intStockCount AS intStockCount,
goods.intCost * data_stock.intStockCount AS intTotalCost,
goods.intPrice * data_stock.intStockCount AS intTotalPrice
FROM data_stock data_stock
LEFT JOIN goods ON data_stock.chrStockCode = goods.chrID
AND data_stock.chrDate = '2015/08/21'
AND goods.chrSeller_ID = '026'
AND goods.chrMaker_ID = '083'
AND goods.chrGroup_ID = '07'
AND data_stock.chrShop_ID = '00'
LEFT JOIN seller seller ON goods.chrSeller_ID = seller.chrID
LEFT JOIN maker maker ON goods.chrMaker_ID = maker.chrID
LEFT JOIN `group` `group` ON goods.chrGroup_ID = `group`.chrID
LEFT JOIN shop shop ON data_stock.chrShop_ID = shop.chrID LIMIT 10;


SELECT data_stock.chrStockCode AS chrID,
goods.chrCode AS chrCode,
goods.chrName AS chrName,
`group`.`chrName` AS chrGroup,
seller.chrName AS chrSeller,
maker.chrID As makerID,
maker.chrName AS chrMaker,
goods.chrColor AS chrColor,
goods.chrSize AS chrSize,
goods.intCost AS intCost,
goods.intPrice AS intPrice,
data_stock.intStockCount AS intStockCount,
goods.intCost * data_stock.intStockCount AS intTotalCost,
goods.intPrice * data_stock.intStockCount AS intTotalPrice
FROM data_stock data_stock
LEFT JOIN goods ON data_stock.chrStockCode = goods.chrID
AND data_stock.chrDate = '2015/08/24'
AND goods.chrMaker_ID = '083'
AND goods.chrSeller_ID = '026'
AND `goods`.`chrGroup_ID` = 01
AND data_stock.chrShop_ID = '01'
LEFT JOIN seller seller ON goods.chrSeller_ID = seller.chrID
LEFT JOIN maker maker ON goods.chrMaker_ID = maker.chrID
LEFT JOIN `group` ON goods.chrGroup_ID = `group`.`chrID`
LEFT JOIN shop shop ON data_stock.chrShop_ID = shop.chrID LIMIT 10;