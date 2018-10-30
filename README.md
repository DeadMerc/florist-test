# Test for florist

### Stage 1

Store new order
```bash
php project.php create_order --item="Носки женские" --amount=2 --total=1500.00
```
View order
```bash
php project.php list_order
```

### Stage 2

Store discount card
```bash
php project.php create_card --num=111222555 --discount=15

```
Store new order with discount
```bash
php project.php create_order --item="Носки женские" --amount=2 --total=1500.00 --discount_card=111222555
php project_white.php create_order --item="Носки женские" --amount=2 --total=1500.00 --discount_card=211222555
```

### Stage 3

Store discount card by white project
```bash
php project_white.php create_card --num=211222555 --discount=35

```

### Stage 4

Store in project pink and calculate discount by sum orders
```bash
php project_pink.php create_order  --item="Носки женские" --amount=2 --total=100 --discount_card=111222555
```

Store in project white and calculate discount by all orders via specific card
```bash
php project_white.php create_order  --item="Носки женские" --amount=2 --total=100 --discount_card=111222555
```

Store in project blue and take discount only from card
```bash
php project_blue.php create_order --item="Blue test" --amount=1 --total=100 --discount_card=211222555
```

