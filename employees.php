<?php

/**
 * The purpose of this code is to implement a solution for Problem #3 of the Problem Statement.
 *
 * The problem statement does not specify any displaying, printing, or logging of any results so I do not do
 * any of that. I do however have some testing and if the testing finds any problems, it will squeal.
 *
 * One issue with the problem statement is that "employee_type" is basically encoded in two locations.
 * That is, there is an explicit employee_type assigned to each employee _and_ the employee record
 * contains salary, commission, and bonus information that implies the employee_type.  This gives rise
 * to the possibility that these two sources of information could be inconsistent.  For example, a Manager
 * might have commission information in the db. Do we ignore the commission because he's a Manager? Although this
 * is not a problem with the very small quantity of given example data, in real life this would be a nettlesome
 * issue.
 *
 * Given the problem statement as it exists now, we _could_ actually ignore the employee_type information and produce correct
 * results from the employee table only. However, the problem statement also _suggests_ building a solution that
 * is extensible with more employee_types and calculations.  It would in fact make a lot of sense to use
 * an employee_type to very clearly and unambiguously specify employee_type and to serve as a basis for parameter
 * checking of the employee table. So therefore I hereby resolve
 * this puzzle by doing exactly that.
 *
 * Another issue is that the calculation for commission looks wrong.  In real-life I'd want to seek clarification.
 * For this example, I'll just implement as specified.
 *
 * The problem also does not mention anything about parameter checking or error handling. I therefore ignore
 * those issues.
 */

/**
 * The purpose of this interface is to mandate functionality for calculating a bonus amount.
 */
interface BonusEarner {
  function calcBonus();
}

/**
 * The purpose of this interface is to mandate functionality for calculating a commission.
 */
interface CommissionEarner {
  function calcCommission();
}

/**
 * The purpose of this class is to model Employees generally and to serve as the base class
 * for more specialized Employee subclasses.
 */
abstract class Employee {
  const ENGINEER    = 1;
  const MANAGER     = 2;
  const SALESPERSON = 3;

  // This looks like an error, but it's what is specified, and it should be a constant from this class.
  const COMMISSION  = 2000;

  // Every employee gets at least a monthly_salary
  protected $monthly_salary;

  /**
   * All Employees have at least a monthly_salary.
   * @return the monthly salary;
   */
  public function calcPay() {
    return $this->monthly_salary;
  }
}

/**
 * An Engineer is just an ordinary Employee.
 */
class Engineer extends Employee {

  public function __construct($employee_record) {
    $this->monthly_salary = $employee_record['month_salary'];
  }
}

/**
 * A Manager is an Employee that can receive a bonus.
 */
class Manager extends Employee implements BonusEarner {
  private $bonus;
  public function __construct($employee_record) {
    $this->monthly_salary = $employee_record['month_salary'];
    $this->bonus = $employee_record['bonus'];
  }
  public function calcBonus() {
    return $this->bonus;
  }

  public function calcPay() {
    return parent::calcPay() + $this->calcBonus();
  }
}

/**
 * A Salesperson is an Employee that can receive a commission.
 */
class Salesperson extends Employee implements CommissionEarner  {
  private $commission;
  public function __construct($employee_record) {
    $this->monthly_salary = $employee_record['month_salary'];
    $this->commission = $employee_record['commission'];
  }
  public function calcCommission() {
    return $this->commission;
  }

  public function calcPay() {
    return (parent::calcPay() * EMPLOYEE::COMMISSION) / $this->calcCommission();
  }
}

/**
 * This class encapsulates information required to build an SQL statement that will retrieve
 * the single row, from the db, for a given employee_id.
 */
class SQLSelectEmployee {
  private $sql_string;
  private $employee_id;

  /**
   * SQLSelectEmployee constructor.
   * @param $sql_string
   * @param $employee_id
   */
  public function __construct($sql_string, $employee_id) {
    $this->sql_string = $sql_string;
    $this->employee_id = $employee_id;
  }

  public function get_sql_string() {
    return $this->sql_string;
  }

  public function get_employee_id() {
    return $this->employee_id;
  }

}

/**
 * This class serves as a mock for a real Database/Connection object.
 *
 * It has a single static function GetRow, as mandated by the problem statement.
 *
 * Although the single GetRow method is static, I instantiate an object of this class for actual usage
 * via dependency-injection.  This will make our lives as testers easier.
 *
 */
class Database {

  /**
   * The problem statement is rather vague about the nature of GetRow so I've thus taken the liberty of making some assumptions
   * that make this work more smoothly.
   *
   * 1. What is the type of the $sql parameter?  Is it a string? Is it an object that represents an SQL query?
   *    In the absence of guidance, I choose the later.  I do so because GetRow needs the employee_id to do its
   *    job.  If I don't feed employee_id as a parameter, then I must pick it out of a string of SQL.
   *
   * 2. What is the return type of GetRow?  I assume an object of type Employee or a subclass.
   *
   * I have also taken the liberty of providing a denormalized edition of the example data from the problem statement
   * since I think this is a test of PHP, not manipulation of SQL.
   *
   * @param $sql SQLSelectEmployee
   *
   * @return An object of type Employee or a subclass of same.
   */
  static function GetRow($sql) {
    $mock_data = [
      ['employee_id'=>1, 'employee_type'=>Employee::ENGINEER,    'month_salary'=>8000,  'commission'=>null, 'bonus'=>null],
      ['employee_id'=>2, 'employee_type'=>Employee::MANAGER,     'month_salary'=>10000, 'commission'=>null, 'bonus'=>2000],
      ['employee_id'=>3, 'employee_type'=>Employee::SALESPERSON, 'month_salary'=>6000,  'commission'=>2000, 'bonus'=>null]
    ];

    $employee_id = $sql->get_employee_id();
    $employee_ids = array_column($mock_data, 'employee_id');
    $employee_idx = array_search($employee_id, $employee_ids);
    $employee_record = $mock_data[$employee_idx];
    $employee_type = $employee_record['employee_type'];

    switch($employee_type) {
      case Employee::ENGINEER:
        return new Engineer($employee_record);
      case Employee::MANAGER:
        return new Manager($employee_record);
      case Employee::SALESPERSON:
        return new Salesperson($employee_record);
      default:
        print "Error: Unknown employee_type $employee_type";
    }
  }
}


/**
 * If you want to know somebody's pay, talk to the PayrollDepartment.
 */
class PayrollDepartment {

  // We need a db/connection to get employee info.
  private $db;

  public function __construct($db) {
    $this->db = $db;
  }

  /**
   * This function will compute the salary of a given employee_id and compare that
   * with an expectedResult.
   *
   * @param $employee_id
   * @param $expectedResult
   * @return 0 if the salary is computed as expected, else 1.
   */
  function calculateAndVerifySalary($employee_id, $expectedResult) {
    $result = $this->calculateSalary($employee_id);
    if($result != $expectedResult) {
      print "Error: calculateSalary($employee_id) sb $expectedResult, but instead it is $result\n";
      return 1;
    }
    return 0;
  }

  /**
   * Calculate an employee's pay.  Build a suitable query for the $db, invoke the query to retrieve
   * an employee object, and then ask that object to calculate its pay.
   *
   * @param $employee_id
   * @return the pay amount.
   */
  public function calculateSalary($employee_id) {

    $sql = new SQLSelectEmployee(
        "select * from employee as e left join employee_type as et on e.employee_type_id = et.id where e.id = $employee_id",
        $employee_id
    );

    $employee = $this->db->GetRow($sql);
    return $employee->calcPay();
  }
}

// Entry point
$db = new Database();             // We will need a mock db/connection to get employee data.
$pd = new PayrollDepartment($db); // The PayrollDepartment knows everybody's pay, and it needs a db to do its work.

$error_cnt = 0;
$error_cnt += $pd->calculateAndVerifySalary(1, 8000); // employee_id 1 is expected to have a salary of 8000
$error_cnt += $pd->calculateAndVerifySalary(2, 12000);
$error_cnt += $pd->calculateAndVerifySalary(3, 6000);
if($error_cnt == 0) {print "Tests complete. No errors.";}
?>
