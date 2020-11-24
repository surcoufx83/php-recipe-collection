function eatercalc(value, eater, neweater) {
  if (eater == neweater)
    return value
  if (neweater <= 0)
    return 0
  var calc = value * neweater / eater
  var factor = evaluateFactor(calc)
  // console.log(value, neweater, eater, calc, factor)
  if (calc < .01)
    return calc
  calc = Math.round(calc / factor) * factor
  return calc
}

function evaluateFactor(value) {
  if (value < .1)
    return .01
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
