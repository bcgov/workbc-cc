# rds

resource "aws_db_subnet_group" "data_subnet" {
  name                   = "data-subnet"
  subnet_ids             = module.network.aws_subnet_ids.data.ids

  tags = local.common_tags
}

/*resource "aws_rds_cluster" "postgres" {
  cluster_identifier      = "ceu-postgres-cluster"
  engine                  = "aurora-postgresql"
  engine_mode             = "serverless"
  engine_version          = "13.6"
  database_name           = "drupal"
  scaling_configuration {
    auto_pause               = true
    max_capacity             = 64
    min_capacity             = 2
    seconds_until_auto_pause = 300
    timeout_action           = "ForceApplyCapacityChange"
  }
  master_username         = local.db_creds.adm_username
  master_password         = local.db_creds.adm_password
  backup_retention_period = 5
  preferred_backup_window = "07:00-09:00"
  db_subnet_group_name    = aws_db_subnet_group.data_subnet.name
  kms_key_id              = aws_kms_key.workbc-cc-kms-key.arn
  storage_encrypted       = true
  vpc_security_group_ids  = [data.aws_security_group.data.id]
  skip_final_snapshot     = true
  final_snapshot_identifier = "ceu-finalsnapshot"

  tags = local.common_tags
}*/

# create this manually
data "aws_secretsmanager_secret_version" "creds" {
  secret_id = "workbc-cc-db-creds"
}

locals {
  db_creds = jsondecode(
    data.aws_secretsmanager_secret_version.creds.secret_string
  )
}
