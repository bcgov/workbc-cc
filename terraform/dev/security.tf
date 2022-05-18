# security.tf

# ALB Security Group: Edit to restrict access to the application
data "aws_security_group" "web" {
  name = "Web_sg"
}

# Traffic to the ECS cluster should only come from the ALB
resource "aws_security_group" "ecs_tasks" {
  name        = "workbc-cc-ecs-tasks-security-group"
  description = "allow inbound access from the ALB only"
  vpc_id      = module.network.aws_vpc.id

  ingress {
    protocol        = "tcp"
    from_port       = var.app_port
    to_port         = var.app_port
    security_groups = [data.aws_security_group.web.id]
  }

  egress {
    protocol    = "-1"
    from_port   = 0
    to_port     = 0
    cidr_blocks = ["0.0.0.0/0"]
  }

  tags = local.common_tags
}


resource "aws_security_group" "rds_security_group" {
  name        = "workbc-cc-rds-security-group"
  description = "allow inbound access from the ECS tasks"
  vpc_id      = module.network.aws_vpc.id

  ingress {
    protocol        = "tcp"
    from_port       = 3306
    to_port         = 3306
    security_groups = [aws_security_group.ecs_tasks.id]
  }

  egress {
    protocol        = "tcp"
    from_port       = 3306
    to_port         = 3306
    security_groups = [aws_security_group.ecs_tasks.id]
  }

  tags = local.common_tags
}


resource "aws_security_group" "efs_security_group" {
  name        = "workbc-cc-efs-security-group"
  description = "allow inbound access from the ECS tasks"
  vpc_id      = module.network.aws_vpc.id

  ingress {
    protocol        = "tcp"
    from_port       = 2049
    to_port         = 2049
    security_groups = [aws_security_group.ecs_tasks.id]
    description     = "For enabling EFS access"
  }

  egress {
    protocol        = "tcp"
    from_port       = 2049
    to_port         = 2049
    security_groups = [aws_security_group.ecs_tasks.id]
    description     = "For enabling EFS access"
  }

  tags = local.common_tags
}
