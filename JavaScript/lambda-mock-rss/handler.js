"use strict";
var fs = require("fs");

module.exports.hello = async (event) => {
  const file = await fs.readFileSync("mock-rss.xml", "utf8");
  console.log({ file });
  return {
    statusCode: 200,
    body: file,
  };

  // Use this code if you don't use the http event with the LAMBDA-PROXY integration
  // return { message: 'Go Serverless v1.0! Your function executed successfully!', event };
};
module.exports.rss2 = async (event) => {
  const file = await fs.readFileSync("mock-rss-2.xml", "utf8");
  console.log({ file });
  return {
    statusCode: 200,
    body: file,
  };

  // Use this code if you don't use the http event with the LAMBDA-PROXY integration
  // return { message: 'Go Serverless v1.0! Your function executed successfully!', event };
};
