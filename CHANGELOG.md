# Change Log

## [1.0.0-RC2](https://github.com/php-task/TaskBundle/tree/1.0.0-RC2) (2017-02-27)
[Full Changelog](https://github.com/php-task/TaskBundle/compare/1.0.0-RC1...1.0.0-RC2)

**Merged pull requests:**

- Fixed query to find pending executions [\#36](https://github.com/php-task/TaskBundle/pull/36) ([wachterjohannes](https://github.com/wachterjohannes))

## [1.0.0-RC1](https://github.com/php-task/TaskBundle/tree/1.0.0-RC1) (2017-02-13)
[Full Changelog](https://github.com/php-task/TaskBundle/compare/0.3.1...1.0.0-RC1)

**Implemented enhancements:**

- Time measurement [\#11](https://github.com/php-task/TaskBundle/issues/11)
- Clear entity-manager after each task to ensure clean environment  [\#34](https://github.com/php-task/TaskBundle/pull/34) ([wachterjohannes](https://github.com/wachterjohannes))

**Closed issues:**

- Description for the commands should be more specific. [\#31](https://github.com/php-task/TaskBundle/issues/31)

**Merged pull requests:**

- Updated composer to match symfony 3 [\#33](https://github.com/php-task/TaskBundle/pull/33) ([wachterjohannes](https://github.com/wachterjohannes))
- Added command descriptions [\#32](https://github.com/php-task/TaskBundle/pull/32) ([wachterjohannes](https://github.com/wachterjohannes))
- Added find-by-task-uuid function [\#30](https://github.com/php-task/TaskBundle/pull/30) ([wachterjohannes](https://github.com/wachterjohannes))
- Rename findByStartTime method in repository [\#29](https://github.com/php-task/TaskBundle/pull/29) ([wachterjohannes](https://github.com/wachterjohannes))
- Added selective flush to repositories [\#28](https://github.com/php-task/TaskBundle/pull/28) ([wachterjohannes](https://github.com/wachterjohannes))
- Removed flush of repositories [\#27](https://github.com/php-task/TaskBundle/pull/27) ([wachterjohannes](https://github.com/wachterjohannes))
- Added find-by-task and remove function [\#26](https://github.com/php-task/TaskBundle/pull/26) ([wachterjohannes](https://github.com/wachterjohannes))
- Added find-by-uuid to task-repository [\#25](https://github.com/php-task/TaskBundle/pull/25) ([wachterjohannes](https://github.com/wachterjohannes))
- Used uuid as doctrine-identifier [\#24](https://github.com/php-task/TaskBundle/pull/24) ([wachterjohannes](https://github.com/wachterjohannes))

## [0.3.1](https://github.com/php-task/TaskBundle/tree/0.3.1) (2016-11-07)
[Full Changelog](https://github.com/php-task/TaskBundle/compare/0.3.0...0.3.1)

**Merged pull requests:**

- Updated required php version to allow 5.5 [\#22](https://github.com/php-task/TaskBundle/pull/22) ([janmassive](https://github.com/janmassive))

## [0.3.0](https://github.com/php-task/TaskBundle/tree/0.3.0) (2016-10-15)
[Full Changelog](https://github.com/php-task/TaskBundle/compare/0.2.1...0.3.0)

**Fixed bugs:**

- Cant use schema update when have tagged services [\#8](https://github.com/php-task/TaskBundle/issues/8)

**Closed issues:**

- Functional testcase for TaskCompilerPass [\#5](https://github.com/php-task/TaskBundle/issues/5)

**Merged pull requests:**

- Changed usage of task-builder because of reintroduce of schedule [\#21](https://github.com/php-task/TaskBundle/pull/21) ([wachterjohannes](https://github.com/wachterjohannes))
- Improved Tests with unit and functional tests [\#20](https://github.com/php-task/TaskBundle/pull/20) ([wachterjohannes](https://github.com/wachterjohannes))
- Improved architecture of bundle [\#15](https://github.com/php-task/TaskBundle/pull/15) ([wachterjohannes](https://github.com/wachterjohannes))

## [0.2.1](https://github.com/php-task/TaskBundle/tree/0.2.1) (2016-03-30)
[Full Changelog](https://github.com/php-task/TaskBundle/compare/0.2.0...0.2.1)

**Closed issues:**

- Debug tasks should be sorted DESC for execution date [\#17](https://github.com/php-task/TaskBundle/issues/17)
- Schedule task command option e \(end-date\) bug [\#16](https://github.com/php-task/TaskBundle/issues/16)

**Merged pull requests:**

- Fixed command issues [\#18](https://github.com/php-task/TaskBundle/pull/18) ([wachterjohannes](https://github.com/wachterjohannes))

## [0.2.0](https://github.com/php-task/TaskBundle/tree/0.2.0) (2016-02-27)
[Full Changelog](https://github.com/php-task/TaskBundle/compare/0.1.0...0.2.0)

**Fixed bugs:**

- If handle return object output failed [\#7](https://github.com/php-task/TaskBundle/issues/7)
- Commands should only output something on error or in verbosity mode [\#6](https://github.com/php-task/TaskBundle/issues/6)

**Closed issues:**

- Move command names to service definition [\#4](https://github.com/php-task/TaskBundle/issues/4)

**Merged pull requests:**

- Added command and extended storage [\#14](https://github.com/php-task/TaskBundle/pull/14) ([wachterjohannes](https://github.com/wachterjohannes))
- Added options to command schedule task for cron tasks [\#13](https://github.com/php-task/TaskBundle/pull/13) ([wachterjohannes](https://github.com/wachterjohannes))

## [0.1.0](https://github.com/php-task/TaskBundle/tree/0.1.0) (2016-01-31)
**Fixed bugs:**

- Next Execution date should be in the future [\#12](https://github.com/php-task/TaskBundle/issues/12)

**Merged pull requests:**

- Add tagged service task compilerpass [\#2](https://github.com/php-task/TaskBundle/pull/2) ([wachterjohannes](https://github.com/wachterjohannes))



\* *This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)*