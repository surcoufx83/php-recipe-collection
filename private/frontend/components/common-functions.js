function eatercalc(value, eater, neweater) {
  if (eater == neweater)
    return value
  if (neweater <= 0)
    return 0
  var calc = value * neweater / eater
  var factor = evaluateFactor(calc)
  // console.log(value, eater, neweater)
  // console.log(calc, factor)
  if (calc < 1)
    return calc
  calc = Math.round(calc / factor) * factor

  return calc
}

function evaluateFactor(value) {
  if (value < 1)
    return .1
  if (value < 2)
    return .5
  if (value < 10)
    return 1
  if (value < 25)
    return 2.5
  if (value < 50)
    return 5
  if (value < 100)
    return 10
  if (value < 1000)
    return 25
  return 50
}

function validEmail(email) {
  var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  return re.test(email);
}
