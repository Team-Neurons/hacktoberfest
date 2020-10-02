function calculate() {
  var num1 = document.getElementById("in1").value;
  var oparator = document.getElementById("oparator").value;
  var num2 = document.getElementById("in2").value;

  if (isNaN(num1) || isNaN(num2)) {
    alert("Not a number");
  } else if (oparator === "+") {
    var sum = 0;
    sum = parseInt(num1) + parseInt(num2);
    document.getElementById("sum").innerHTML = "Summary=" + sum;
  } else if (oparator === "-") {
    var sum = 0;
    sum = parseInt(num1) - parseInt(num2);
    document.getElementById("sum").innerHTML = "Summary=" + sum;
  } else if (oparator === "*") {
    var sum = 0;
    sum = parseInt(num1) * parseInt(num2);
    document.getElementById("sum").innerHTML = "Summary=" + sum;
  } else if (oparator === "/") {
    var sum = 0;
    sum = parseInt(num1) * parseInt(num2);
    document.getElementById("sum").innerHTML = "Summary=" + sum;
  } else if (oparator === "%") {
    var sum = 0;
    sum = parseInt(num1) * parseInt(num2);
    document.getElementById("sum").innerHTML = "Summary=" + sum;
  } else {
    alert("Not a oparator format");
  }
  document.getElementById("pattern").innerHTML =
    "Pattern=" + parseInt(num1) + oparator + parseInt(num2);
}
