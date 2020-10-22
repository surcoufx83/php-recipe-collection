<?php

namespace Surcouf\Cookbook\Database\Builder;

if (!defined('CORE2'))
  exit;

final class EExpressionType {
  const etNONE = 0;
  const etOR = 1;
  const etAND = 2;
  const etEQUALS = 3;
  const etIS = 4;
  const etNOT = 5;
  const etISNOT = 6;
  const etST = 7;
  const etSEQUALT = 8;
  const etLT = 9;
  const etLEQUALT = 10;
  const etIN = 11;
  const etNOTIN = 12;
  const etBETWEEN = 13;
  const etNOTBETWEEN = 14;
  const etLIKE = 15;
  const etNOTLIKE = 16;
  const etCONTAINS = 17;

  public static function getString(int $expr) : string {
    switch($expr) {
      case EExpressionType::etOR:
        return 'OR';
      case EExpressionType::etAND:
        return 'AND';
      case EExpressionType::etEQUALS:
        return '=';
      case EExpressionType::etIS:
        return 'IS';
      case EExpressionType::etNOT:
        return 'NOT';
      case EExpressionType::etISNOT:
        return 'IS NOT';
      case EExpressionType::etST:
        return '<';
      case EExpressionType::etSEQUALT:
        return '<=';
      case EExpressionType::etLT:
        return '>';
      case EExpressionType::etLEQUALT:
        return '>=';
      case EExpressionType::etIN:
        return 'IN';
      case EExpressionType::etNOTIN:
        return 'NOT IN';
      case EExpressionType::etBETWEEN:
        return 'BETWEEN';
      case EExpressionType::etNOTBETWEEN:
        return 'NOT BETWEEN';
      case EExpressionType::etLIKE:
        return 'LIKE';
      case EExpressionType::etCONTAINS:
        return 'LIKE';
      case EExpressionType::etNOTLIKE:
        return 'NOT LIKE';

    }
  }

}
