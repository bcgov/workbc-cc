resource "aws_kms_key" "workbc-cc-kms-key" {
  description             = "KMS Key for Career Discovery Quizzes"
  deletion_window_in_days = 10
  enable_key_rotation     = true
  tags = local.common_tags
}