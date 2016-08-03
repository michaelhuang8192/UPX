package  {
	
	import flash.events.*;
	import flash.external.*
	
	
	public class FileEventHandler
	{
		public var fid:uint;
		public var sts:int;
		public var prc:Number;
		public var res:String;

		public function FileEventHandler()
		{
			this.fid = 0;
			this.sts = 0;
			this.prc = 0.0;
			this.res = '';
		}
		
		public function completeHandler(event:Event):void
		{
			//ExternalInterface.call("uploader_log", '' + event);
			this.sts = 1;
		}

		public function progressHandler(event:ProgressEvent):void
		{
			//ExternalInterface.call("uploader_log", this.fid + ' ' + event);
			this.prc = event.bytesLoaded / event.bytesTotal * 100.0;
		}

		public function uploadCompleteDataHandler(event:DataEvent):void
		{
			//ExternalInterface.call("uploader_log", '' + event);
			this.sts = 1;
			this.res = event.data;
		}
		
		public function ioErrorHandler(event:Event):void
		{
			//ExternalInterface.call("uploader_log", '' + event);
			this.sts = -1;
		}
		
	}
	
}

