package com.aws.bucket;

import java.util.Scanner;

import com.amazonaws.AmazonServiceException;
import com.amazonaws.auth.AWSCredentials;
import com.amazonaws.auth.AWSCredentialsProvider;
import com.amazonaws.auth.ClasspathPropertiesFileCredentialsProvider;
import com.amazonaws.auth.PropertiesCredentials;
import com.amazonaws.services.cloudfront.AmazonCloudFrontClient;
import com.amazonaws.services.cloudfront.model.Aliases;
import com.amazonaws.services.cloudfront.model.CreateCloudFrontOriginAccessIdentityRequest;
import com.amazonaws.services.cloudfront.model.CreateDistributionRequest;
import com.amazonaws.services.cloudfront.model.CreateDistributionResult;
import com.amazonaws.services.cloudfront.model.CreateStreamingDistributionRequest;
import com.amazonaws.services.cloudfront.model.DistributionConfig;
import com.amazonaws.services.cloudfront.model.ListDistributionsRequest;
import com.amazonaws.services.cloudfront.model.Origin;
import com.amazonaws.services.cloudfront.model.Origins;
import com.amazonaws.services.cloudfront.model.S3Origin;
import com.amazonaws.services.cloudfront.model.S3OriginConfig;
import com.amazonaws.services.cloudfront.model.StreamingDistributionConfig;
//import com.amazonaws.services.cloudfront.model.S3Origin;
import com.amazonaws.services.ec2.AmazonEC2;
import com.amazonaws.services.ec2.AmazonEC2Client;

import com.amazonaws.services.s3.AmazonS3Client;

public class Bucket {

	/**
	 * @param args
	 */
	static AmazonEC2 ec2;
	static AmazonS3Client s3;

	public static void main(String[] args) throws Exception {

//		AWSCredentialsProvider credentialsProvider = new ClasspathPropertiesFileCredentialsProvider();
		
		try {
//			AmazonCloudFrontClient cloudfront = new AmazonCloudFrontClient(credentialsProvider);
//			 
//			// Create a new CloudFront Distribution
//		
//			cloudfront.createDistribution(new CreateDistributionRequest(new DistributionConfig()
//			    .withCallerReference("unique-id-for-idempotency")
//			    .withComment("my first CloudFront distribution")
//			    .withDefaultRootObject("index.html")
//			    .withEnabled(true)
//			    .withS3Origin(new S3Origin("newcloudfront.s3.amazonaws.com"))
//			));
//
//			// List existing CloudFront Distributions
//			System.out.println("Distributions: " + cloudfront.listDistributions());
			AWSCredentialsProvider credentialsProvider = new ClasspathPropertiesFileCredentialsProvider();
//			AWSCredentials credentials = new PropertiesCredentials(Bucket.class.getResourceAsStream("AwsCredentials.properties"));
			
			s3 = new AmazonS3Client(credentialsProvider);
			String bucketName="";
			while (true) {
				System.out.println("Input a bucket name:");
				Scanner scanInput = new Scanner(System.in);
				 bucketName = scanInput.nextLine();
				try {
					s3.createBucket(bucketName);
					System.out.println("Your bucket:" + bucketName + " is created.");
					break;
				} catch (AmazonServiceException ase) {
					System.out.println("fail to create bucket, try again.");
					

				}
			}
			
			
			
			AmazonCloudFrontClient cloudfront = new AmazonCloudFrontClient(credentialsProvider);
	        Origin origin = new Origin()
	        .withDomainName(bucketName+"s3.amazonaws.com")
	        .withId(bucketName)
	        .withS3OriginConfig(new S3OriginConfig().withOriginAccessIdentity(""));
//
			Origins origins = new Origins().withItems(origin);
//
			cloudfront.createDistribution(new CreateDistributionRequest(new DistributionConfig()
		    .withCallerReference("unique-id-for-idempotency")
		    .withComment("my first CloudFront distribution")
		    .withDefaultRootObject("index.html")
		    .withEnabled(true)
		    .withOrigins(origins)
		));
			
			ListDistributionsRequest lDRequest = new ListDistributionsRequest();
			lDRequest.withMaxItems("1");
;			System.out.println("Distributions: " + cloudfront.listDistributions(lDRequest));
//			DistributionConfig streamingDistributionConfig = new DistributionConfig()
//					.withCallerReference("unique-id-for-idempotency")
//					.withComment("Streaming CloudFront distribution")
//					.withDefaultRootObject("index.html").withEnabled(true)
//					.withOrigins(origins);

			StreamingDistributionConfig streamingDistributionConfig2 = new StreamingDistributionConfig();
//			 include the with parameters
			CreateStreamingDistributionRequest streamingDistribution2 = new CreateStreamingDistributionRequest()
					.withStreamingDistributionConfig(streamingDistributionConfig2);
			cloudfront.createStreamingDistribution(streamingDistribution2);
			
			
		} catch (AmazonServiceException ase) {
			System.out.println("Caught Exception: " + ase.getMessage());
			System.out.println("Reponse Status Code: " + ase.getStatusCode());
			System.out.println("Error Code: " + ase.getErrorCode());
			System.out.println("Request ID: " + ase.getRequestId());
		}

	}

}
